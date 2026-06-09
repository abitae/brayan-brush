import { Head } from '@inertiajs/react';
import BrayanBrushLayout from '@/layouts/brayan-brush-layout';
import AdminDashboard from '@/components/brayan-brush/AdminDashboard';
import type {
  AgencyItem,
  CalculatorCityItem,
  ComplaintItem,
  PricingRouteItem,
  ProhibitedCategoryAdmin,
  QuoteItem,
  ServiceItem,
  SiteConfig,
} from '@/api/brayan-api';

interface AdminProps {
  config: SiteConfig;
  services: ServiceItem[];
  prohibitedCategories: ProhibitedCategoryAdmin[];
  quotes: QuoteItem[];
  pricingRoutes: PricingRouteItem[];
  calculatorCities: CalculatorCityItem[];
  agencies: AgencyItem[];
  complaints: ComplaintItem[];
}

export default function Admin({
  config,
  services,
  prohibitedCategories,
  quotes,
  pricingRoutes,
  calculatorCities,
  agencies,
  complaints,
}: AdminProps) {
  return (
    <BrayanBrushLayout>
      <Head title="Panel Administrativo - Brayan Brush" />
      <AdminDashboard
        config={config}
        services={services}
        prohibitedCategories={prohibitedCategories}
        quotes={quotes}
        pricingRoutes={pricingRoutes}
        calculatorCities={calculatorCities}
        agencies={agencies}
        complaints={complaints}
      />
    </BrayanBrushLayout>
  );
}
