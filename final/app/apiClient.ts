/**
 * Cliente de la API Brayan Brush (FastAPI).
 * Base URL: VITE_API_URL o por defecto http://localhost:8000
 */
const API_BASE = typeof import.meta !== "undefined" && import.meta.env?.VITE_API_URL
  ? import.meta.env.VITE_API_URL.replace(/\/$/, "")
  : "http://localhost:8000";

async function fetchApi<T>(
  path: string,
  options: RequestInit = {}
): Promise<T> {
  const url = `${API_BASE}${path.startsWith("/") ? path : `/${path}`}`;
  const res = await fetch(url, {
    ...options,
    headers: {
      "Content-Type": "application/json",
      ...options.headers,
    },
  });
  if (!res.ok) {
    const err = await res.json().catch(() => ({ detail: res.statusText }));
    throw new Error(err.detail || res.statusText);
  }
  return res.json();
}

async function uploadFile(
  path: string,
  file: File
): Promise<{ url: string }> {
  const url = `${API_BASE}${path.startsWith("/") ? path : `/${path}`}`;
  const form = new FormData();
  form.append("file", file);
  const res = await fetch(url, {
    method: "POST",
    body: form,
  });
  if (!res.ok) {
    const err = await res.json().catch(() => ({ detail: res.statusText }));
    throw new Error(err.detail || res.statusText);
  }
  return res.json();
}

export interface SiteConfig {
  company_name: string;
  logo_text: string;
  hero_title: string;
  hero_subtitle: string;
  primary_color: string;
  logo_url?: string | null;
  banner_url?: string | null;
  banner_bg_url?: string | null;
}

/** Obtener configuración del sitio */
export async function getConfig(): Promise<SiteConfig> {
  return fetchApi<SiteConfig>("/config");
}

/** Actualizar configuración del sitio */
export async function updateConfig(config: SiteConfig): Promise<{ message: string }> {
  return fetchApi<{ message: string }>("/config", {
    method: "POST",
    body: JSON.stringify(config),
  });
}

/** Subir logo; devuelve la URL pública del archivo */
export async function uploadLogo(file: File): Promise<{ url: string }> {
  return uploadFile("/config/upload-logo", file);
}

/** Subir banner principal */
export async function uploadBanner(file: File): Promise<{ url: string }> {
  return uploadFile("/config/upload-banner", file);
}

/** Subir imagen de fondo del hero */
export async function uploadBannerBg(file: File): Promise<{ url: string }> {
  return uploadFile("/config/upload-banner-bg", file);
}

/** Enviar mensaje al asistente IA (Gemini en backend) */
export async function assistantChat(message: string): Promise<string> {
  const data = await fetchApi<{ text: string }>("/assistant/chat", {
    method: "POST",
    body: JSON.stringify({ message }),
  });
  return data.text;
}
