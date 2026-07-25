<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class MedicalDocumentController extends Controller
{
    private const STORAGE_PREFIX = 'disk:';

    private const ALLOWED_EXTENSIONS = [
        'pdf', 'jpg', 'jpeg', 'png', 'heic', 'heif',
    ];

    public function create(Appointment $appointment)
    {
        $this->ensureDoctorOwnsAppointment($appointment);

        if (!$this->canUploadDocument($appointment)) {
            return redirect()
                ->route('doctor.appointments.show', $appointment)
                ->withErrors([
                    'document' => 'Medical documents can only be uploaded after the patient has checked in.',
                ]);
        }

        return view('doctor.documents.create', [
            'appointment' => $appointment->load('patient'),
        ]);
    }

    public function store(Request $request, Appointment $appointment)
    {
        $this->ensureDoctorOwnsAppointment($appointment, $request);

        if (!$this->canUploadDocument($appointment)) {
            return redirect()
                ->route('doctor.appointments.show', $appointment)
                ->withErrors([
                    'document' => 'Medical documents can only be uploaded after the patient has checked in.',
                ]);
        }

        $data = $request->validate([
            'document' => ['required', 'file', 'max:5120'],
            'document_type' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('document');

        if (!$file || !$file->isValid()) {
            throw ValidationException::withMessages([
                'document' => 'The uploaded document is invalid. Please choose it again.',
            ]);
        }

        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw ValidationException::withMessages([
                'document' => 'Upload a PDF, JPG, JPEG, PNG, HEIC, or HEIF file.',
            ]);
        }

        $storedPath = null;

        try {
            $filename = Str::uuid()->toString() . '.' . $extension;
            $storedPath = $file->storeAs(
                'medical-documents/' . $appointment->id,
                $filename,
                'local'
            );

            if (!$storedPath || !Storage::disk('local')->exists($storedPath)) {
                throw new \RuntimeException('The uploaded file was not written to storage.');
            }

            $appointment->documents()->create([
                'uploaded_by' => $request->user()->id,
                'document_type' => $data['document_type'] ?? null,
                'filename' => $this->safeOriginalFilename(
                    $file->getClientOriginalName(),
                    $extension
                ),
                'mime_type' => $file->getMimeType()
                    ?: $file->getClientMimeType()
                    ?: 'application/octet-stream',
                'size_bytes' => $file->getSize(),
                'document_data' => self::STORAGE_PREFIX . $storedPath,
                'uploaded_at' => now(),
            ]);
        } catch (Throwable $exception) {
            if ($storedPath && Storage::disk('local')->exists($storedPath)) {
                Storage::disk('local')->delete($storedPath);
            }

            Log::error('Medical document upload failed.', [
                'appointment_id' => $appointment->id,
                'doctor_id' => $request->user()->id,
                'original_name' => $file?->getClientOriginalName(),
                'size' => $file?->getSize(),
                'error' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'document' => 'The document could not be saved. Please try a smaller file or convert it to PDF/JPG.',
                ]);
        }

        return redirect()
            ->route('doctor.appointments.show', $appointment)
            ->with('status', 'Medical document uploaded successfully.');
    }

    public function show(MedicalDocument $document)
    {
        $document->load('appointment');
        $user = auth()->user();

        if (
            !$user ||
            (!$user->isDoctor()
                && $document->appointment->patient_id !== $user->id)
        ) {
            abort(403);
        }

        $data = $document->document_data;

        if (is_string($data) && str_starts_with($data, self::STORAGE_PREFIX)) {
            $path = substr($data, strlen(self::STORAGE_PREFIX));

            if (
                !$path ||
                str_contains($path, '..') ||
                !str_starts_with($path, 'medical-documents/') ||
                !Storage::disk('local')->exists($path)
            ) {
                abort(404, 'Medical document file not found.');
            }

            return response()->file(
                Storage::disk('local')->path($path),
                [
                    'Content-Type' => $document->mime_type
                        ?: 'application/octet-stream',
                    'Content-Disposition' => $this->contentDisposition(
                        $document->filename
                    ),
                    'X-Content-Type-Options' => 'nosniff',
                    'Cache-Control' => 'private, no-store, max-age=0',
                ]
            );
        }

        if ($data === null || $data === '') {
            abort(404, 'Medical document file not found.');
        }

        return response($data, Response::HTTP_OK, [
            'Content-Type' => $document->mime_type
                ?: 'application/octet-stream',
            'Content-Disposition' => $this->contentDisposition(
                $document->filename
            ),
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }

    private function ensureDoctorOwnsAppointment(
        Appointment $appointment,
        ?Request $request = null
    ): void {
        $userId = $request?->user()?->id ?? auth()->id();

        if ((int) $appointment->doctor_id !== (int) $userId) {
            abort(403);
        }
    }

    private function canUploadDocument(Appointment $appointment): bool
    {
        return (bool) $appointment->checked_in_at
            || in_array(
                $appointment->status,
                ['checked-in', 'completed'],
                true
            );
    }

    private function safeOriginalFilename(
        string $originalName,
        string $extension
    ): string {
        $base = pathinfo($originalName, PATHINFO_FILENAME);
        $base = trim(preg_replace('/[^\pL\pN._ -]+/u', '', $base) ?? '');
        $base = $base !== '' ? mb_substr($base, 0, 180) : 'medical-document';

        return $base . '.' . $extension;
    }

    private function contentDisposition(?string $filename): string
    {
        $filename = $filename ?: 'medical-document';
        $fallback = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename)
            ?: 'medical-document';
        $encoded = rawurlencode($filename);

        return 'inline; filename="' . $fallback
            . '"; filename*=UTF-8\'\'' . $encoded;
    }
}
