<?php

namespace App\Models;

use App\Traits\Relations\BelongsToOne\BelongsFile;
use App\Traits\Relations\BelongsToOne\BelongsTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CV extends Model
{
    use SoftDeletes, BelongsFile, BelongsTemplate;

    protected $fillable = [
        'user_id', 'file_id', 'template_id', 'personal_details', 'employment_history', 'education',
        'skills', 'summary', 'additional_sections', 'customize', 'slug', 'ready',
    ];

    protected $table = 'cvs';

    protected $casts = [
        'personal_details' => 'array',
        'employment_history' => 'array',
        'education' => 'array',
        'skills' => 'array',
        'additional_sections' => 'array',
        'customize' => 'array',
        'ready' => 'boolean',
    ];

//    Structure
/**
 * Templates:-
 * - name
 *
 * CVs:-
 * - user_id
 * - template_id
 * - personal_details
 * - employment_history[]
 * - education[]
 * - skills[]
 * - summary (longText)
 * - additional_sections[]
 * - customize
 * - slug
 * - ready
 * - deleted_at
 *
 * Personal Details:-
 * - job_title
 * - avatar
 * - first_name
 * - last_name
 * - email
 * - phone
 * - address
 * - city/state
 * - Country
 * ___ Add more details ___
 * - zip_code
 * - driving_license
 * - place_of_birth
 * - date_of_birth
 * - nationality
 *
 * Employment History:-
 * - job_title
 * - employer/company
 * - start_date
 * - end_date
 * - city
 * - description [richtext HTML]
 * ___ Add one more employment ___
 *
 * Education:-
 * - school
 * - degree
 * - start_date
 * - end_date
 * - city
 * - description [richtext HTML]
 * ___ Add one more education ___
 *
 * Skills:-
 * - skill
 * - level [Novice|Beginner|Skillful|Experienced|Expert]
 * ___ Add one more skill ___
 *
 * Professional Summary (longText) [RichText HTML]
 *
 * Additional Sections:-
 * (1) Courses:
 * - course
 * - institution
 * - start_date
 * - end_date
 * ___ Add one more course ___
 *
 * (2) Internships:
 * - job_title
 * - employer
 * - start_date
 * - end_date
 * - city
 * - description [richtext HTML]
 * ___ Add one more internship ___
 *
 * (3) Languages:
 * - language
 * - level (Select level, Native speaker, Highly proficient, Very good command, Good working knowledge, Working knowledge, C2, C1, B2, B1, A2, A1)
 * ___ Add one more language ___
 *
 * (4) Hobbies (wha do you like?)
 *
 * Customize:-
 * - color
 * - font_family
 * - secondary_font
 * - size
 * - spacing
 */
}
