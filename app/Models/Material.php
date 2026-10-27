<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Material extends Model
{
    use HasFactory;

    // New uploads are private; older uploads may still live on the public disk.
    public const DISK = 'local';
    public const LEGACY_DISK = 'public';

    protected $fillable = [
        'title',
        'description',
        'file',
        'user_id',
        'class_id',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classess::class, 'class_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /** Name of the disk that currently holds the attachment, or null. */
    public function fileDisk(): ?string
    {
        if (! $this->file) {
            return null;
        }

        foreach ([self::DISK, self::LEGACY_DISK] as $disk) {
            if (Storage::disk($disk)->exists($this->file)) {
                return $disk;
            }
        }

        return null;
    }

    public function fileExtension(): ?string
    {
        return $this->file ? strtolower(pathinfo($this->file, PATHINFO_EXTENSION)) : null;
    }

    public function isImage(): bool
    {
        return in_array($this->fileExtension(), ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
    }

    public function deleteFile(): void
    {
        if ($disk = $this->fileDisk()) {
            Storage::disk($disk)->delete($this->file);
        }
    }
}
