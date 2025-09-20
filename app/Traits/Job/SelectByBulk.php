<?php

namespace App\Traits\Job;

use App\Models\JobApp;

trait SelectByBulk
{
    public $selectAll = false;
    public $selected = [];
    public $bulkAction = '';

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getJobs()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function updatedSelected()
    {
        if (count($this->selected) == $this->perPage){
            $this->selectAll = true;
        }else{
            $this->selectAll = false;
        }
    }

    public function applyBulkAction()
    {
        if (empty($this->selected) || empty($this->bulkAction)) {
            $this->dispatch('swal:error', [
                'title' => __('words.No items selected or no action chosen'),
                'text' => __("words.Please select items and an action"),
                'icon' => 'warning',
            ]);
            return;
        }

        switch ($this->bulkAction) {
            case 'delete':
                $this->bulkDelete();
                break;
        }

        // Reset selection after action
        $this->selectAll = false;
        $this->selected = [];
        $this->bulkAction = '';
    }

    public function bulkDelete()
    {
        $count = JobApp::whereIn('id', $this->selected)->delete();

        $this->dispatch('swal:success', [
            'title' => __("words.(count) item action successfully!", ["action" => __('words.deleted'), "count" => $count, "item" => __("words.Jobs")]),
            'text' => '',
            'icon' => 'success',
        ]);
    }
}
