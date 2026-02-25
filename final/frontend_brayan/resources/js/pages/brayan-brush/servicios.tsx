import { Head } from '@inertiajs/react';
import BrayanBrushLayout from '@/layouts/brayan-brush-layout';
import ServicesSection from '@/components/brayan-brush/ServicesSection';

interface ServiciosProps {
  services: { id: string; title: string; description: string; icon_type: string }[];
}

export default function Servicios({ services }: ServiciosProps) {
  return (
    <BrayanBrushLayout>
      <Head title="Servicios - Brayan Brush" />
      <ServicesSection items={services} />
    </BrayanBrushLayout>
  );
}
