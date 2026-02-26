<?php

namespace App\Livewire\Configuration;

use App\Models\Configuration\TipoEntryCaja;
use App\Models\Configuration\TipoExitCaja;
use Livewire\Component;
use Livewire\WithPagination;

class TipoCajaLive extends Component
{
    use WithPagination;

    // Propiedades para tipos de entrada
    public $entry_id;
    public $entry_name = '';
    public $entry_is_active = true;
    public $showEntryModal = false;
    public $isEditingEntry = false;

    // Propiedades para tipos de salida
    public $exit_id;
    public $exit_name = '';
    public $exit_is_active = true;
    public $showExitModal = false;
    public $isEditingExit = false;

    // Mensajes
    public $message = '';

    protected $rules = [
        'entry_name' => 'required|string|max:255',
        'entry_is_active' => 'boolean',
        'exit_name' => 'required|string|max:255',
        'exit_is_active' => 'boolean',
    ];

    protected $messages = [
        'entry_name.unique' => 'El nombre del tipo de entrada ya existe.',
        'exit_name.unique' => 'El nombre del tipo de salida ya existe.',
    ];

    // Métodos para tipos de entrada
    public function openEntryModal($id = null)
    {
        $this->resetEntryForm();
        if ($id) {
            $entry = TipoEntryCaja::findOrFail($id);
            $this->entry_id = $entry->id;
            $this->entry_name = $entry->name;
            $this->entry_is_active = $entry->is_active;
            $this->isEditingEntry = true;
        } else {
            $this->isEditingEntry = false;
        }
        $this->showEntryModal = true;
    }

    public function saveEntry()
    {
        $rules = [
            'entry_name' => 'required|string|max:255|unique:tipo_entry_cajas,name' . ($this->isEditingEntry && $this->entry_id ? ',' . $this->entry_id : ''),
            'entry_is_active' => 'boolean',
        ];
        $validated = $this->validate($rules);
        if ($this->isEditingEntry && $this->entry_id) {
            $entry = TipoEntryCaja::findOrFail($this->entry_id);
            $entry->update([
                'name' => $this->entry_name,
                'is_active' => $this->entry_is_active,
            ]);
            $this->message = 'Tipo de entrada actualizado correctamente.';
        } else {
            TipoEntryCaja::create([
                'name' => $this->entry_name,
                'is_active' => $this->entry_is_active,
            ]);
            $this->message = 'Tipo de entrada creado correctamente.';
        }
        $this->closeEntryModal();
    }

    public function deleteEntry($id)
    {
        $entry = TipoEntryCaja::findOrFail($id);
        $entry->delete();
        $this->message = 'Tipo de entrada eliminado correctamente.';
    }

    public function toggleEntryActive($id)
    {
        $entry = TipoEntryCaja::findOrFail($id);
        $entry->is_active = !$entry->is_active;
        $entry->save();
        $this->message = 'Estado actualizado.';
    }

    public function closeEntryModal()
    {
        $this->showEntryModal = false;
        $this->resetEntryForm();
    }

    public function resetEntryForm()
    {
        $this->entry_id = null;
        $this->entry_name = '';
        $this->entry_is_active = true;
        $this->isEditingEntry = false;
    }

    // Métodos para tipos de salida
    public function openExitModal($id = null)
    {
        $this->resetExitForm();
        if ($id) {
            $exit = TipoExitCaja::findOrFail($id);
            $this->exit_id = $exit->id;
            $this->exit_name = $exit->name;
            $this->exit_is_active = $exit->is_active;
            $this->isEditingExit = true;
        } else {
            $this->isEditingExit = false;
        }
        $this->showExitModal = true;
    }

    public function saveExit()
    {
        $rules = [
            'exit_name' => 'required|string|max:255|unique:tipo_exit_cajas,name' . ($this->isEditingExit && $this->exit_id ? ',' . $this->exit_id : ''),
            'exit_is_active' => 'boolean',
        ];
        $validated = $this->validate($rules);
        if ($this->isEditingExit && $this->exit_id) {
            $exit = TipoExitCaja::findOrFail($this->exit_id);
            $exit->update([
                'name' => $this->exit_name,
                'is_active' => $this->exit_is_active,
            ]);
            $this->message = 'Tipo de salida actualizado correctamente.';
        } else {
            TipoExitCaja::create([
                'name' => $this->exit_name,
                'is_active' => $this->exit_is_active,
            ]);
            $this->message = 'Tipo de salida creado correctamente.';
        }
        $this->closeExitModal();
    }

    public function deleteExit($id)
    {
        $exit = TipoExitCaja::findOrFail($id);
        $exit->delete();
        $this->message = 'Tipo de salida eliminado correctamente.';
    }

    public function toggleExitActive($id)
    {
        $exit = TipoExitCaja::findOrFail($id);
        $exit->is_active = !$exit->is_active;
        $exit->save();
        $this->message = 'Estado actualizado.';
    }

    public function closeExitModal()
    {
        $this->showExitModal = false;
        $this->resetExitForm();
    }

    public function resetExitForm()
    {
        $this->exit_id = null;
        $this->exit_name = '';
        $this->exit_is_active = true;
        $this->isEditingExit = false;
    }

    public function render()
    {
        $tiposEntry = TipoEntryCaja::orderBy('name')->get();
        $tiposExit = TipoExitCaja::orderBy('name')->get();
        $message = $this->message;
        $this->message = '';
        return view('livewire.configuration.tipo-caja-live', compact('tiposEntry', 'tiposExit', 'message'));
    }
}
