import { useState } from 'react';
import { getTracking } from '@/api/brayan-api';
import type { TrackingResult } from '@/api/brayan-api';
import { ICONS } from '@/constants/brayan';

const DOCUMENT_REGEX = /^\d{8,11}$/;

const STATUS_STEPS = [
  { key: 'registrado', label: 'Registrado' },
  { key: 'enviado', label: 'Enviado' },
  { key: 'recibido', label: 'Recibido' },
  { key: 'entregado', label: 'Entregado' },
] as const;

function normalizeTrackingResult(data: unknown): TrackingResult {
  const d = data as Record<string, unknown>;
  const history = Array.isArray(d?.history)
    ? (d.history as { date?: string; location?: string; desc?: string }[]).map((h) => ({
        date: h?.date ?? '',
        location: h?.location ?? '',
        desc: h?.desc ?? '',
      }))
    : [];
  return {
    code: typeof d?.code === 'string' ? d.code : '',
    status: typeof d?.status === 'string' ? d.status : 'registrado',
    status_label: typeof d?.status_label === 'string' ? d.status_label : 'Registrado',
    current_location: d?.current_location != null ? String(d.current_location) : null,
    origin: typeof d?.origin === 'string' ? d.origin : '—',
    destination: typeof d?.destination === 'string' ? d.destination : '—',
    name_origen: d?.name_origen != null ? String(d.name_origen) : null,
    name_destino: d?.name_destino != null ? String(d.name_destino) : null,
    estimated_delivery: d?.estimated_delivery != null ? String(d.estimated_delivery) : null,
    progress: typeof d?.progress === 'number' ? d.progress : 0,
    history,
    is_home: typeof d?.is_home === 'boolean' ? d.is_home : false,
    delivery_address: d?.delivery_address != null ? String(d.delivery_address) : null,
  };
}

function getStatusBadgeClass(status: string): string {
  switch (status) {
    case 'registrado':
      return 'bg-slate-500/20 text-slate-300 border-slate-500/50';
    case 'enviado':
      return 'bg-amber-500/20 text-amber-300 border-amber-500/50';
    case 'recibido':
      return 'bg-blue-500/20 text-blue-300 border-blue-500/50';
    case 'retornado':
      return 'bg-orange-500/20 text-orange-300 border-orange-500/50';
    case 'entregado':
      return 'bg-emerald-500/20 text-emerald-300 border-emerald-500/50';
    default:
      return 'bg-slate-500/20 text-slate-300 border-slate-500/50';
  }
}

function statusStepIndex(status: string): number {
  const map: Record<string, number> = {
    registrado: 0,
    enviado: 1,
    recibido: 2,
    entregado: 3,
    retornado: 2,
  };
  return map[status] ?? 0;
}

export default function TrackingSection() {
  const [trackingCode, setTrackingCode] = useState('');
  const [document, setDocument] = useState('');
  const [result, setResult] = useState<TrackingResult | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(false);

  const handleReset = () => {
    setTrackingCode('');
    setDocument('');
    setResult(null);
    setError(null);
  };

  const handleTrack = async () => {
    const code = trackingCode.trim();
    const doc = document.trim();
    if (!code || !doc) {
      setError('Ingresa el código de guía y tu DNI o RUC.');
      return;
    }
    if (!DOCUMENT_REGEX.test(doc)) {
      setError('El DNI o RUC debe tener entre 8 y 11 dígitos.');
      return;
    }

    setError(null);
    setResult(null);
    setIsLoading(true);
    try {
      const data = await getTracking(code, doc);
      setResult(normalizeTrackingResult(data));
    } catch (err) {
      setError(err instanceof Error ? err.message : 'No se pudo consultar el seguimiento.');
    } finally {
      setIsLoading(false);
    }
  };

  const BoxIcon = ICONS.Box;
  const MapPinIcon = ICONS.MapPin;
  const SearchIcon = ICONS.Search;
  const currentStep = result ? statusStepIndex(result.status) : -1;

  return (
    <section className="py-16 md:py-24 bg-white relative">
      <div className="max-w-6xl mx-auto px-4">
        <div className="text-center mb-12 md:mb-16">
          <p className="text-emerald-600 font-black text-[10px] uppercase tracking-[0.35em] mb-4">
            Seguimiento en línea
          </p>
          <h2 className="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">
            Rastrea tu <span className="text-emerald-600 font-black">encomienda</span>
          </h2>
          <p className="text-slate-500 text-lg max-w-2xl mx-auto">
            Ingresa el código de guía y el DNI o RUC del remitente o destinatario para consultar el estado de tu envío.
          </p>
        </div>

        <div className="bg-slate-50 rounded-[40px] p-8 md:p-14 border border-slate-100 shadow-2xl shadow-slate-200/50 mb-12">
          <form
            onSubmit={(e) => {
              e.preventDefault();
              handleTrack();
            }}
            className="space-y-6"
          >
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="space-y-2">
                <label htmlFor="rastreo-codigo" className="block text-sm font-bold text-slate-700">
                  Código de guía *
                </label>
                <div className="relative group">
                  <div className="absolute inset-y-0 left-5 flex items-center text-slate-300 group-focus-within:text-emerald-500 transition-colors">
                    <SearchIcon />
                  </div>
                  <input
                    id="rastreo-codigo"
                    type="text"
                    value={trackingCode}
                    onChange={(e) => setTrackingCode(e.target.value.toUpperCase())}
                    placeholder="Ej. H28-11, LIM-312"
                    autoComplete="off"
                    className="w-full pl-14 pr-5 py-5 bg-white border-2 border-slate-200 text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 rounded-2xl outline-none shadow-sm font-bold tracking-wide transition-all"
                  />
                </div>
              </div>
              <div className="space-y-2">
                <label htmlFor="rastreo-documento" className="block text-sm font-bold text-slate-700">
                  DNI o RUC *
                </label>
                <input
                  id="rastreo-documento"
                  type="text"
                  inputMode="numeric"
                  value={document}
                  onChange={(e) => setDocument(e.target.value.replace(/\D/g, ''))}
                  placeholder="Documento del remitente o destinatario"
                  maxLength={11}
                  autoComplete="off"
                  className="w-full px-5 py-5 bg-white border-2 border-slate-200 text-slate-900 placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 rounded-2xl outline-none shadow-sm font-bold tracking-wide transition-all"
                />
              </div>
            </div>

            <div className="flex flex-col sm:flex-row gap-4">
              <button
                type="submit"
                disabled={isLoading}
                className="flex-1 bg-emerald-600 text-white px-10 py-5 rounded-2xl font-black text-lg hover:bg-emerald-500 transition-all shadow-xl shadow-emerald-600/20 active:scale-[0.98] flex items-center justify-center gap-3 disabled:opacity-70"
              >
                {isLoading ? (
                  <div className="w-6 h-6 border-4 border-white/30 border-t-white rounded-full animate-spin" />
                ) : (
                  <>
                    <SearchIcon />
                    Rastrear encomienda
                  </>
                )}
              </button>
              {(result || trackingCode || document) && (
                <button
                  type="button"
                  onClick={handleReset}
                  disabled={isLoading}
                  className="sm:w-auto px-10 py-5 rounded-2xl font-black text-slate-600 bg-white border-2 border-slate-200 hover:border-slate-400 hover:text-slate-900 transition-all disabled:opacity-70"
                >
                  Nueva búsqueda
                </button>
              )}
            </div>
          </form>

          <p className="text-xs text-slate-500 mt-5">
            Los datos deben coincidir con los registrados al momento del envío. Si no encuentras tu encomienda, verifica
            el código y el documento ingresado.
          </p>
        </div>

        {error && (
          <div className="mb-8 p-6 rounded-3xl bg-rose-50 border border-rose-200 text-rose-800 font-medium flex items-start gap-3">
            <span className="text-xl shrink-0">!</span>
            <span>{error}</span>
          </div>
        )}

        {!result && !error && !isLoading && (
          <div className="text-center py-16 px-6 rounded-[40px] border-2 border-dashed border-slate-200 bg-slate-50/50">
            <div className="w-20 h-20 bg-white rounded-3xl border border-slate-100 shadow-sm flex items-center justify-center mx-auto mb-6 text-emerald-600">
              <BoxIcon />
            </div>
            <h3 className="text-xl font-black text-slate-800 mb-2">Consulta el estado de tu paquete</h3>
            <p className="text-slate-500 max-w-md mx-auto text-sm leading-relaxed">
              Estados disponibles: Registrado, Enviado, Recibido, Entregado o Retornado. Completa el formulario para ver
              el detalle y el historial del envío.
            </p>
          </div>
        )}

        {result && (
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 animate-in fade-in duration-500">
            <div className="lg:col-span-8 bg-white rounded-[40px] border border-slate-100 shadow-xl overflow-hidden">
              <div className="bg-slate-900 p-8 md:p-10 text-white flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                  <p className="text-emerald-500 font-black text-[10px] uppercase tracking-[0.3em] mb-2">
                    Código de guía
                  </p>
                  <h3 className="text-2xl md:text-3xl font-black tracking-tighter">{result.code}</h3>
                </div>
                <div
                  className={`backdrop-blur-md px-6 py-4 rounded-2xl border ${getStatusBadgeClass(result.status)}`}
                >
                  <p className="font-black text-[10px] uppercase tracking-[0.2em] mb-1">Estado actual</p>
                  <p className="text-xl font-black">{result.status_label}</p>
                </div>
              </div>

              <div className="px-8 md:px-10 py-8 border-b border-slate-100">
                <div className="flex items-center justify-between gap-2">
                  {STATUS_STEPS.map((step, idx) => {
                    const isActive = idx <= currentStep && result.status !== 'retornado';
                    const isRetornado = result.status === 'retornado' && idx === 2;
                    const highlighted = isActive || isRetornado;
                    return (
                      <div key={step.key} className="flex-1 flex flex-col items-center relative">
                        {idx < STATUS_STEPS.length - 1 && (
                          <div
                            className={`absolute top-4 left-1/2 w-full h-0.5 ${
                              idx < currentStep && result.status !== 'retornado'
                                ? 'bg-emerald-500'
                                : 'bg-slate-200'
                            }`}
                          />
                        )}
                        <div
                          className={`relative z-10 w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-black border-2 ${
                            highlighted
                              ? isRetornado
                                ? 'bg-orange-500 border-orange-200 text-white'
                                : 'bg-emerald-500 border-emerald-200 text-white'
                              : 'bg-white border-slate-200 text-slate-400'
                          }`}
                        >
                          {idx + 1}
                        </div>
                        <p
                          className={`mt-2 text-[9px] md:text-[10px] font-black uppercase tracking-tight text-center ${
                            highlighted ? 'text-slate-800' : 'text-slate-400'
                          }`}
                        >
                          {step.label}
                        </p>
                      </div>
                    );
                  })}
                </div>
                {result.status === 'retornado' && (
                  <p className="mt-4 text-center text-sm font-bold text-orange-600">
                    Esta encomienda fue marcada como retornada.
                  </p>
                )}
              </div>

              <div className="p-8 md:p-10">
                <div className="flex justify-between items-center mb-14 relative px-2">
                  <div className="absolute top-1/2 left-0 right-0 h-1.5 bg-slate-100 -translate-y-1/2 z-0 rounded-full" />
                  <div
                    className="absolute top-1/2 left-0 h-1.5 bg-emerald-500 -translate-y-1/2 z-0 rounded-full transition-all duration-1000"
                    style={{ width: `${result.progress}%` }}
                  />
                  <div className="z-10 text-center max-w-[45%]">
                    <div className="w-14 h-14 md:w-16 md:h-16 bg-white border-4 border-emerald-500 rounded-full flex items-center justify-center text-emerald-600 shadow-lg mx-auto mb-3">
                      <BoxIcon />
                    </div>
                    <p className="text-xs md:text-sm font-black text-slate-900 leading-tight">
                      {result.name_origen || result.origin}
                    </p>
                    <p className="text-[9px] text-slate-400 font-bold uppercase mt-1">Origen</p>
                  </div>
                  <div className="z-10 text-center max-w-[45%]">
                    <div className="w-14 h-14 md:w-16 md:h-16 bg-white border-4 border-slate-100 rounded-full flex items-center justify-center text-slate-300 shadow-sm mx-auto mb-3">
                      <MapPinIcon />
                    </div>
                    <p className="text-xs md:text-sm font-black text-slate-700 leading-tight">
                      {result.name_destino || result.destination}
                    </p>
                    <p className="text-[9px] text-slate-400 font-bold uppercase mt-1">Destino</p>
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                  <div className="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                    <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                      Sucursal de origen
                    </p>
                    <p className="font-black text-slate-900 text-sm mb-1">
                      {result.name_origen || '—'}
                    </p>
                    <p className="text-sm text-slate-600 leading-relaxed">{result.origin}</p>
                  </div>
                  <div className="bg-slate-50 p-6 rounded-3xl border border-slate-100">
                    <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                      Sucursal de destino
                    </p>
                    <p className="font-black text-slate-900 text-sm mb-1">
                      {result.name_destino || '—'}
                    </p>
                    <p className="text-sm text-slate-600 leading-relaxed">{result.destination}</p>
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-slate-50">
                  <div className="bg-slate-50 p-6 rounded-3xl">
                    <p className="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                      Ubicación actual
                    </p>
                    <p className="font-bold text-slate-800 flex items-center gap-2">
                      {result.status !== 'entregado' && result.status !== 'retornado' && (
                        <span className="w-2 h-2 bg-emerald-500 rounded-full animate-ping shrink-0" />
                      )}
                      {result.current_location ?? '—'}
                    </p>
                  </div>
                  <div className="bg-emerald-50 p-6 rounded-3xl">
                    <p className="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-2">
                      Tipo de entrega
                    </p>
                    <p className="font-black text-emerald-900">
                      {result.is_home ? 'Reparto a domicilio' : 'Retiro en agencia'}
                    </p>
                    {result.is_home && result.delivery_address && (
                      <p className="text-sm text-emerald-800 mt-2 leading-relaxed">{result.delivery_address}</p>
                    )}
                  </div>
                </div>
              </div>
            </div>

            <div className="lg:col-span-4 bg-slate-50 rounded-[40px] p-8 md:p-10 border border-slate-100">
              <h4 className="font-black text-slate-900 uppercase tracking-widest text-xs mb-8 pb-4 border-b border-slate-200">
                Historial del envío
              </h4>
              <div className="space-y-8">
                {!(result.history?.length) ? (
                  <p className="text-slate-500 text-sm">Sin eventos registrados aún.</p>
                ) : (
                  (result.history ?? []).map((step, idx) => (
                    <div key={idx} className="flex gap-5 relative">
                      <div className="relative z-10 shrink-0">
                        <div
                          className={`w-4 h-4 rounded-full mt-1 border-4 ${
                            idx === result.history.length - 1
                              ? 'bg-emerald-500 border-emerald-100'
                              : 'bg-slate-300 border-white'
                          }`}
                        />
                        {idx < result.history.length - 1 && (
                          <div className="absolute top-5 left-[7px] bottom-[-32px] w-0.5 bg-slate-200" />
                        )}
                      </div>
                      <div className="min-w-0">
                        <p className="text-[10px] font-black text-slate-400 uppercase tracking-tighter">
                          {step.date}
                        </p>
                        <p className="font-black text-slate-800 text-sm mb-1">{step.location}</p>
                        <p className="text-xs text-slate-500 font-medium leading-relaxed">{step.desc}</p>
                      </div>
                    </div>
                  ))
                )}
              </div>
            </div>
          </div>
        )}
      </div>
    </section>
  );
}
