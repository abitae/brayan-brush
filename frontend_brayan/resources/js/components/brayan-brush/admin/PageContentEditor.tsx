import type { PageContent } from '@/types/page-content';

interface PageContentEditorProps {
  content: PageContent;
  onChange: (content: PageContent) => void;
  onUploadAbout?: (file: File) => void;
  uploadingAbout?: boolean;
}

function Field({
  label,
  value,
  onChange,
  multiline = false,
}: {
  label: string;
  value: string;
  onChange: (v: string) => void;
  multiline?: boolean;
}) {
  const className =
    'w-full bg-slate-50 border-none rounded-2xl px-5 py-3 focus:ring-2 focus:ring-emerald-500 outline-none text-sm';
  return (
    <div className="space-y-1.5">
      <label className="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{label}</label>
      {multiline ? (
        <textarea rows={3} value={value} onChange={(e) => onChange(e.target.value)} className={className} />
      ) : (
        <input type="text" value={value} onChange={(e) => onChange(e.target.value)} className={className} />
      )}
    </div>
  );
}

function Section({ title, children }: { title: string; children: React.ReactNode }) {
  return (
    <div className="rounded-2xl border border-slate-200 p-6 space-y-4">
      <h4 className="text-sm font-black text-slate-800 uppercase tracking-wider">{title}</h4>
      {children}
    </div>
  );
}

export default function PageContentEditor({ content, onChange, onUploadAbout, uploadingAbout }: PageContentEditorProps) {
  const patch = <K extends keyof PageContent>(section: K, key: keyof PageContent[K], value: string) => {
    onChange({
      ...content,
      [section]: { ...content[section], [key]: value },
    });
  };

  return (
    <div className="space-y-8">
      <Section title="Hero (inicio)">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Field label="Etiqueta superior" value={content.hero.badge} onChange={(v) => patch('hero', 'badge', v)} />
          <Field
            label="Texto tarjeta flotante"
            value={content.hero.float_badge}
            onChange={(v) => patch('hero', 'float_badge', v)}
          />
          <Field
            label="Estadística 1 - valor"
            value={content.hero.stat_1_value}
            onChange={(v) => patch('hero', 'stat_1_value', v)}
          />
          <Field
            label="Estadística 1 - etiqueta"
            value={content.hero.stat_1_label}
            onChange={(v) => patch('hero', 'stat_1_label', v)}
          />
          <Field
            label="Estadística 2 - valor"
            value={content.hero.stat_2_value}
            onChange={(v) => patch('hero', 'stat_2_value', v)}
          />
          <Field
            label="Estadística 2 - etiqueta"
            value={content.hero.stat_2_label}
            onChange={(v) => patch('hero', 'stat_2_label', v)}
          />
          <Field
            label="Estadística 3 - valor"
            value={content.hero.stat_3_value}
            onChange={(v) => patch('hero', 'stat_3_value', v)}
          />
          <Field
            label="Estadística 3 - etiqueta"
            value={content.hero.stat_3_label}
            onChange={(v) => patch('hero', 'stat_3_label', v)}
          />
          <Field
            label="Título imagen hero"
            value={content.hero.image_caption_title}
            onChange={(v) => patch('hero', 'image_caption_title', v)}
          />
          <Field
            label="Ciudades imagen hero"
            value={content.hero.image_caption_cities}
            onChange={(v) => patch('hero', 'image_caption_cities', v)}
          />
          <div className="md:col-span-2">
            <Field
              label="Descripción imagen hero"
              value={content.hero.image_caption_text}
              onChange={(v) => patch('hero', 'image_caption_text', v)}
              multiline
            />
          </div>
        </div>
      </Section>

      <Section title="Portafolio estratégico">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Field
            label="Etiqueta"
            value={content.portfolio.eyebrow}
            onChange={(v) => patch('portfolio', 'eyebrow', v)}
          />
          <Field label="Título" value={content.portfolio.title} onChange={(v) => patch('portfolio', 'title', v)} />
          <div className="md:col-span-2">
            <Field
              label="Subtítulo"
              value={content.portfolio.subtitle}
              onChange={(v) => patch('portfolio', 'subtitle', v)}
              multiline
            />
          </div>
          <Field
            label="CTA - título"
            value={content.portfolio.cta_title}
            onChange={(v) => patch('portfolio', 'cta_title', v)}
          />
          <Field
            label="CTA - texto"
            value={content.portfolio.cta_text}
            onChange={(v) => patch('portfolio', 'cta_text', v)}
            multiline
          />
        </div>
      </Section>

      <Section title="Nosotros">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Field label="Título" value={content.about.title} onChange={(v) => patch('about', 'title', v)} />
          <Field label="Badge imagen" value={content.about.badge} onChange={(v) => patch('about', 'badge', v)} />
          <div className="md:col-span-2">
            <Field label="Párrafo 1 (sin nombre empresa)" value={content.about.text_1} onChange={(v) => patch('about', 'text_1', v)} multiline />
            <Field label="Párrafo 2" value={content.about.text_2} onChange={(v) => patch('about', 'text_2', v)} multiline />
          </div>
          {onUploadAbout && (
            <div className="md:col-span-2">
              <label className="text-[10px] font-bold text-slate-500 uppercase tracking-widest block mb-2">
                Imagen nosotros
              </label>
              {content.about.image_url && (
                <img src={content.about.image_url} alt="Nosotros" className="h-24 w-auto rounded-lg mb-2 object-cover" />
              )}
              <input type="file" accept=".png,.jpg,.jpeg,.webp" onChange={(e) => e.target.files?.[0] && onUploadAbout(e.target.files[0])} disabled={uploadingAbout} />
            </div>
          )}
        </div>
      </Section>

      <Section title="Agencias (textos)">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Field label="Título" value={content.agencies.title} onChange={(v) => patch('agencies', 'title', v)} />
          <Field label="Subtítulo" value={content.agencies.subtitle} onChange={(v) => patch('agencies', 'subtitle', v)} multiline />
          <Field label="CTA título" value={content.agencies.cta_title} onChange={(v) => patch('agencies', 'cta_title', v)} />
          <Field label="CTA botón" value={content.agencies.cta_button} onChange={(v) => patch('agencies', 'cta_button', v)} />
          <div className="md:col-span-2">
            <Field label="CTA texto" value={content.agencies.cta_text} onChange={(v) => patch('agencies', 'cta_text', v)} multiline />
          </div>
        </div>
      </Section>

      <Section title="Contacto / Cotización">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Field label="Título" value={content.contact.title} onChange={(v) => patch('contact', 'title', v)} />
          <Field label="Palabra destacada" value={content.contact.title_highlight} onChange={(v) => patch('contact', 'title_highlight', v)} />
          <div className="md:col-span-2">
            <Field label="Subtítulo" value={content.contact.subtitle} onChange={(v) => patch('contact', 'subtitle', v)} multiline />
          </div>
        </div>
      </Section>

      <Section title="Rastreo">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Field label="Etiqueta" value={content.tracking.eyebrow} onChange={(v) => patch('tracking', 'eyebrow', v)} />
          <Field label="Título" value={content.tracking.title} onChange={(v) => patch('tracking', 'title', v)} />
          <Field label="Palabra destacada" value={content.tracking.title_highlight} onChange={(v) => patch('tracking', 'title_highlight', v)} />
          <Field label="Subtítulo" value={content.tracking.subtitle} onChange={(v) => patch('tracking', 'subtitle', v)} multiline />
        </div>
      </Section>

      <Section title="Cotizador">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Field label="Etiqueta" value={content.calculator.eyebrow} onChange={(v) => patch('calculator', 'eyebrow', v)} />
          <Field label="Título" value={content.calculator.title} onChange={(v) => patch('calculator', 'title', v)} />
          <Field label="Palabra destacada" value={content.calculator.title_highlight} onChange={(v) => patch('calculator', 'title_highlight', v)} />
          <div className="md:col-span-2">
            <Field label="Subtítulo" value={content.calculator.subtitle} onChange={(v) => patch('calculator', 'subtitle', v)} multiline />
          </div>
        </div>
      </Section>

      <Section title="Página de servicios">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Field label="Etiqueta" value={content.services_page.eyebrow} onChange={(v) => patch('services_page', 'eyebrow', v)} />
          <Field label="Título" value={content.services_page.title} onChange={(v) => patch('services_page', 'title', v)} />
          <Field label="Palabra destacada" value={content.services_page.title_highlight} onChange={(v) => patch('services_page', 'title_highlight', v)} />
          <div className="md:col-span-2">
            <Field label="Subtítulo" value={content.services_page.subtitle} onChange={(v) => patch('services_page', 'subtitle', v)} multiline />
          </div>
          <Field label="Característica 1 - título" value={content.services_page.feature_1_title} onChange={(v) => patch('services_page', 'feature_1_title', v)} />
          <Field label="Característica 1 - texto" value={content.services_page.feature_1_text} onChange={(v) => patch('services_page', 'feature_1_text', v)} multiline />
          <Field label="Característica 2 - título" value={content.services_page.feature_2_title} onChange={(v) => patch('services_page', 'feature_2_title', v)} />
          <Field label="Característica 2 - texto" value={content.services_page.feature_2_text} onChange={(v) => patch('services_page', 'feature_2_text', v)} multiline />
          <Field label="Característica 3 - título" value={content.services_page.feature_3_title} onChange={(v) => patch('services_page', 'feature_3_title', v)} />
          <Field label="Característica 3 - texto" value={content.services_page.feature_3_text} onChange={(v) => patch('services_page', 'feature_3_text', v)} multiline />
          <Field label="CTA - título" value={content.services_page.cta_title} onChange={(v) => patch('services_page', 'cta_title', v)} />
          <Field label="CTA - texto" value={content.services_page.cta_text} onChange={(v) => patch('services_page', 'cta_text', v)} multiline />
          <Field label="CTA - botón principal" value={content.services_page.cta_button_primary} onChange={(v) => patch('services_page', 'cta_button_primary', v)} />
          <Field label="CTA - botón secundario" value={content.services_page.cta_button_secondary} onChange={(v) => patch('services_page', 'cta_button_secondary', v)} />
        </div>
      </Section>

      <Section title="Prohibiciones y footer">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Field label="Prohibiciones - etiqueta" value={content.prohibited.eyebrow} onChange={(v) => patch('prohibited', 'eyebrow', v)} />
          <Field label="Prohibiciones - título" value={content.prohibited.title} onChange={(v) => patch('prohibited', 'title', v)} />
          <Field label="Aviso - título" value={content.prohibited.warning_title} onChange={(v) => patch('prohibited', 'warning_title', v)} />
          <Field label="Aviso - botón" value={content.prohibited.warning_button} onChange={(v) => patch('prohibited', 'warning_button', v)} />
          <div className="md:col-span-2">
            <Field label="Aviso - párrafo 1" value={content.prohibited.warning_text_1} onChange={(v) => patch('prohibited', 'warning_text_1', v)} multiline />
            <Field label="Aviso - párrafo 2" value={content.prohibited.warning_text_2} onChange={(v) => patch('prohibited', 'warning_text_2', v)} multiline />
            <Field label="Footer - descripción" value={content.footer.description} onChange={(v) => patch('footer', 'description', v)} multiline />
            <Field label="Footer - sufijo copyright" value={content.footer.copyright_suffix} onChange={(v) => patch('footer', 'copyright_suffix', v)} />
          </div>
        </div>
      </Section>
    </div>
  );
}
