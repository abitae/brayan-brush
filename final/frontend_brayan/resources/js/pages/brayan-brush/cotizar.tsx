import { Head, usePage } from '@inertiajs/react';
import BrayanBrushLayout from '@/layouts/brayan-brush-layout';
import CalculatorSection, { type CalculatorDefaults } from '@/components/brayan-brush/CalculatorSection';
import { submitQuote } from '@/api/brayan-api';

interface CotizarPageProps {
  calculatorDefaults?: CalculatorDefaults | null;
}

export default function Cotizar({ calculatorDefaults }: CotizarPageProps) {
  const pageProps = usePage().props as CotizarPageProps;
  const defaults = calculatorDefaults ?? pageProps.calculatorDefaults ?? undefined;

  const handleQuoteSubmit = async (quote: {
    nombre: string;
    email?: string;
    telefono: string;
    servicio: string;
    mensaje: string;
    estimated_price?: number;
  }) => {
    try {
      await submitQuote(quote);
    } catch (err) {
      alert(err instanceof Error ? err.message : 'Error al enviar la cotización.');
    }
  };

  return (
    <BrayanBrushLayout>
      <Head title="Cotizar - Brayan Brush" />
      <div className="min-h-[80vh] py-10">
        <CalculatorSection onQuoteSubmit={handleQuoteSubmit} calculatorDefaults={defaults} />
      </div>
    </BrayanBrushLayout>
  );
}
