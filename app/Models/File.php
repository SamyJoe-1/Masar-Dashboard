<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'name', 'path', 'fullpath', 'type', 'size'
    ];

    protected $table = 'files';

    public function getType()
    {
        switch ($this->type) {
            case "application/pdf":
                return "pdf";
            case "application/msword":
                return "doc";
            case "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
                return "docx";
            case "application/vnd.ms-excel":
                return "xls";
            case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
                return "xlsx";
            case "application/vnd.ms-powerpoint":
                return "ppt";
            case "application/vnd.openxmlformats-officedocument.presentationml.presentation":
                return "pptx";
            case "text/plain":
                return "txt";
            case "text/csv":
                return "csv";
            case "application/rtf":
                return "rtf";
            case "application/vnd.oasis.opendocument.text":
                return "odt";
            case "application/vnd.oasis.opendocument.spreadsheet":
                return "ods";
            case "application/vnd.oasis.opendocument.presentation":
                return "odp";
            case "image/jpeg":
                return "jpg";
            case "image/pjpeg":
                return "jpg";
            case "image/png":
                return "png";
            case "image/gif":
                return "gif";
            case "image/webp":
                return "webp";
            case "image/bmp":
                return "bmp";
            case "image/tiff":
                return "tiff";
            case "image/svg+xml":
                return "svg";
            default:
                return "unknown";
        }

    }
}
