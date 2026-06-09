import Swal from 'sweetalert2';
import { useState } from 'react';
import { createAgency, deleteAgency, updateAgency, type AgencyItem } from '@/api/brayan-api';

interface AgenciesAdminPanelProps {
  agencies: AgencyItem[];
  onChange: (agencies: AgencyItem[]) => void;
}

const emptyAgency = {
  name: '',
  address: '',
  city: '',
  phone: '',
  lat: -12.0464,
  lng: -77.0428,
  is_active: true,
};

export default function AgenciesAdminPanel({ agencies, onChange }: AgenciesAdminPanelProps) {
  const [newAgency, setNewAgency] = useState(emptyAgency);
  const [editingId, setEditingId] = useState<number | null>(null);

  const handleCreate = async () => {
    if (!newAgency.name.trim()) return;
    try {
      const created = await createAgency(newAgency);
      onChange([...agencies, created]);
      setNewAgency(emptyAgency);
      Swal.fire({ icon: 'success', text: 'Agencia creada.', toast: true, position: 'top-end', timer: 2500, showConfirmButton: false });
    } catch (err) {
      Swal.fire({ icon: 'error', text: err instanceof Error ? err.message : 'Error al crear.' });
    }
  };

  const handleUpdate = async (agency: AgencyItem) => {
    try {
      const updated = await updateAgency(agency.id, agency);
      onChange(agencies.map((a) => (a.id === agency.id ? { ...a, ...updated } : a)));
      setEditingId(null);
      Swal.fire({ icon: 'success', text: 'Agencia actualizada.', toast: true, position: 'top-end', timer: 2500, showConfirmButton: false });
    } catch (err) {
      Swal.fire({ icon: 'error', text: err instanceof Error ? err.message : 'Error al guardar.' });
    }
  };

  const handleDelete = async (id: number) => {
    if (!confirm('¿Eliminar esta agencia?')) return;
    try {
      await deleteAgency(id);
      onChange(agencies.filter((a) => a.id !== id));
    } catch (err) {
      Swal.fire({ icon: 'error', text: err instanceof Error ? err.message : 'Error al eliminar.' });
    }
  };

  const inputClass = 'w-full bg-slate-50 rounded-xl px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-emerald-500';

  return (
    <div className="space-y-8">
      <p className="text-sm text-slate-500">
        Las tarjetas de agencias en inicio y /agencias se gestionan aquí. Los textos de la sección están en &quot;Textos del sitio&quot;.
      </p>

      <div className="rounded-2xl border border-dashed border-slate-200 p-6 space-y-4">
        <h4 className="font-black text-slate-800">Nueva agencia</h4>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
          <input placeholder="Nombre" value={newAgency.name} onChange={(e) => setNewAgency({ ...newAgency, name: e.target.value })} className={inputClass} />
          <input placeholder="Ciudad" value={newAgency.city} onChange={(e) => setNewAgency({ ...newAgency, city: e.target.value })} className={inputClass} />
          <input placeholder="Dirección" value={newAgency.address} onChange={(e) => setNewAgency({ ...newAgency, address: e.target.value })} className={inputClass} />
          <input placeholder="Teléfono" value={newAgency.phone} onChange={(e) => setNewAgency({ ...newAgency, phone: e.target.value })} className={inputClass} />
          <input type="number" step="any" placeholder="Latitud" value={newAgency.lat} onChange={(e) => setNewAgency({ ...newAgency, lat: Number(e.target.value) })} className={inputClass} />
          <input type="number" step="any" placeholder="Longitud" value={newAgency.lng} onChange={(e) => setNewAgency({ ...newAgency, lng: Number(e.target.value) })} className={inputClass} />
        </div>
        <button type="button" onClick={handleCreate} className="bg-emerald-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm">
          Agregar agencia
        </button>
      </div>

      <div className="space-y-4">
        {agencies.map((agency) => (
          <div key={agency.id} className="rounded-2xl border border-slate-200 p-5 space-y-3">
            {editingId === agency.id ? (
              <>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <input value={agency.name} onChange={(e) => onChange(agencies.map((a) => a.id === agency.id ? { ...a, name: e.target.value } : a))} className={inputClass} />
                  <input value={agency.city} onChange={(e) => onChange(agencies.map((a) => a.id === agency.id ? { ...a, city: e.target.value } : a))} className={inputClass} />
                  <input value={agency.address} onChange={(e) => onChange(agencies.map((a) => a.id === agency.id ? { ...a, address: e.target.value } : a))} className={inputClass} />
                  <input value={agency.phone} onChange={(e) => onChange(agencies.map((a) => a.id === agency.id ? { ...a, phone: e.target.value } : a))} className={inputClass} />
                  <input type="number" step="any" value={agency.lat} onChange={(e) => onChange(agencies.map((a) => a.id === agency.id ? { ...a, lat: Number(e.target.value) } : a))} className={inputClass} />
                  <input type="number" step="any" value={agency.lng} onChange={(e) => onChange(agencies.map((a) => a.id === agency.id ? { ...a, lng: Number(e.target.value) } : a))} className={inputClass} />
                </div>
                <div className="flex gap-2">
                  <button type="button" onClick={() => handleUpdate(agency)} className="bg-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-bold">Guardar</button>
                  <button type="button" onClick={() => setEditingId(null)} className="px-4 py-2 rounded-lg text-sm font-bold border">Cancelar</button>
                </div>
              </>
            ) : (
              <div className="flex justify-between items-start gap-4">
                <div>
                  <p className="font-black text-slate-900">{agency.name}</p>
                  <p className="text-sm text-slate-500">{agency.address}, {agency.city}</p>
                  <p className="text-sm text-emerald-700 font-bold mt-1">{agency.phone}</p>
                </div>
                <div className="flex gap-2 shrink-0">
                  <button type="button" onClick={() => setEditingId(agency.id)} className="text-sm font-bold text-emerald-600">Editar</button>
                  <button type="button" onClick={() => handleDelete(agency.id)} className="text-sm font-bold text-rose-600">Eliminar</button>
                </div>
              </div>
            )}
          </div>
        ))}
      </div>
    </div>
  );
}
