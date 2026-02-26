<?php

namespace App\Services\Facturacion;

use App\Models\Facturacion\Note;
use App\Models\Facturacion\NoteDetail;
use Illuminate\Support\Facades\DB;
use Exception;

class NoteService
{
    /**
     * Crea una nota con sus detalles.
     *
     * @param array $data Datos principales de la nota (Note)
     * @param array $detailsData Array de arrays con los detalles (NoteDetail)
     * @return Note
     * @throws Exception
     */
    public function createNote(array $data, array $detailsData): Note
    {
        return DB::transaction(function () use ($data, $detailsData) {
            // Crear la nota principal
            $note = Note::create($data);

            // Crear los detalles
            foreach ($detailsData as $detail) {
                $detail['note_id'] = $note->id;
                NoteDetail::create($detail);
            }

            // Opcional: cargar detalles para retornar con la relación
            $note->load('details');

            return $note;
        });
    }
}
