import { Head } from '@inertiajs/react';
import BrayanBrushLayout from '@/layouts/brayan-brush-layout';
import TrackingSection from '@/components/brayan-brush/TrackingSection';

export default function Rastreo() {
  return (
    <BrayanBrushLayout>
      <Head title="Rastreo de Encomiendas - Brayan Brush" />
      <div className="min-h-[80vh]">
        <TrackingSection />
      </div>
    </BrayanBrushLayout>
  );
}
