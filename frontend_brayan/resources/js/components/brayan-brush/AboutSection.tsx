import { usePageContent } from '@/hooks/use-page-content';

interface AboutSectionProps {
  companyName: string;
}

export default function AboutSection({ companyName }: AboutSectionProps) {
  const about = usePageContent().about;

  return (
    <section className="py-24 bg-white overflow-hidden">
      <div className="max-w-7xl mx-auto px-4">
        <div className="flex flex-col lg:flex-row items-center gap-20">
          <div className="lg:w-1/2 relative">
            <div className="relative z-10 rounded-[40px] overflow-hidden shadow-2xl">
              <img src={about.image_url} alt={`Flota de ${companyName}`} className="w-full h-full object-cover" />
            </div>
            <div className="absolute -bottom-6 -right-6 bg-emerald-600 p-6 rounded-2xl shadow-xl z-20 text-white">
              <p className="text-2xl font-black">{companyName}</p>
              <p className="text-[10px] font-bold uppercase tracking-widest">{about.badge}</p>
            </div>
          </div>

          <div className="lg:w-1/2">
            <h2 className="text-5xl font-black text-slate-900 mb-8 leading-[1.1]">{about.title}</h2>
            <div className="space-y-6 text-slate-600 text-lg leading-relaxed font-medium">
              <p>
                <b>{companyName}</b> {about.text_1}
              </p>
              <p>{about.text_2}</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
