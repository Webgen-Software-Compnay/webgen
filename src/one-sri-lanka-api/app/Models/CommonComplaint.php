<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommonComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_category_id',
    'district_id',
        'title',
        'description',
        'form_data',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'form_data' => 'array',
    ];

    /**
     * Relationship: A complaint belongs to a category
     */
    public function category()
    {
        return $this->belongsTo(ComplaintCategory::class, 'complaint_category_id');
    }

    /**
     * Get the form fields count
     */
    public function getFormFieldsCountAttribute()
    {
        return is_array($this->form_data) ? count($this->form_data) : 0;
    }

    /**
     * Check if the complaint has a configured form
     */
    public function hasFormStructure()
    {
        return !empty($this->form_data) && is_array($this->form_data) && count($this->form_data) > 0;
    }
}
