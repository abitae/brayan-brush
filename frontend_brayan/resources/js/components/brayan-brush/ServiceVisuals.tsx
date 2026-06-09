import type { ReactNode } from 'react';
import { ICONS } from '@/constants/brayan';

export interface ServiceVisual {
  title: string;
  icon_type?: string;
  icon_url?: string | null;
  image_url?: string | null;
}

const ICON_MAP: Record<string, () => ReactNode> = {
  Box: ICONS.Box,
  Home: ICONS.Home,
  Package: ICONS.Package,
};

export function ServiceIconBox({
  service,
  className = 'flex h-full w-full items-center justify-center',
}: {
  service: ServiceVisual;
  className?: string;
}) {
  if (service.icon_url) {
    return <img src={service.icon_url} alt="" className={`${className} object-contain p-1.5`} />;
  }

  const Icon = ICON_MAP[service.icon_type ?? ''] ?? ICONS.Package;
  return (
    <div className={className}>
      <Icon />
    </div>
  );
}

export function ServiceCoverImage({
  service,
  className = 'h-full w-full object-cover',
}: {
  service: ServiceVisual;
  className?: string;
}) {
  if (!service.image_url) {
    return null;
  }

  return <img src={service.image_url} alt={service.title} className={className} />;
}
