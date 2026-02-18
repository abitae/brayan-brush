from fastapi import FastAPI, HTTPException, UploadFile, File
from fastapi.staticfiles import StaticFiles
from pydantic import BaseModel
from typing import List, Optional
import os
import shutil
from datetime import datetime
from fastapi.middleware.cors import CORSMiddleware
from .config import settings
from . import database

app = FastAPI(title="Brayan Brush CMS & Logistics API")


@app.on_event("startup")
def startup():
    """Asegura que las tablas existan al arrancar."""
    if database.USE_MYSQL and database.engine and database.Base:
        database.Base.metadata.create_all(bind=database.engine)

# Gemini (asistente): solo si está configurada la clave
_gemini_model = None
if settings.GEMINI_API_KEY:
    try:
        import google.generativeai as genai
        genai.configure(api_key=settings.GEMINI_API_KEY)
        _gemini_model = genai.GenerativeModel(
            "gemini-1.5-flash",
            system_instruction=(
                "Eres el Asistente Inteligente de Brayan Brush - Corporación Logística, "
                "una empresa líder en Perú especializada exclusivamente en transporte terrestre por camiones. "
                "Tu tono es profesional, servicial y experto. "
                "Ayudas con: transporte de carga por carretera en Perú (Lima, Arequipa, Trujillo, etc.), "
                "seguridad vial, empaque para tránsito terrestre, sostenibilidad en flotas. "
                "Brayan Brush NO realiza envíos aéreos ni marítimos; solo logística terrestre nacional peruana."
            ),
        )
    except Exception:
        _gemini_model = None

# Servir archivos estáticos (imágenes subidas)
if not os.path.exists(settings.UPLOAD_DIR):
    os.makedirs(settings.UPLOAD_DIR)

app.mount("/uploads", StaticFiles(directory=settings.UPLOAD_DIR), name="uploads")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

# Modelos
class SiteConfig(BaseModel):
    company_name: str
    logo_text: str
    hero_title: str
    hero_subtitle: str
    primary_color: str
    logo_url: Optional[str] = None
    banner_url: Optional[str] = None
    banner_bg_url: Optional[str] = None

# Base de datos simulada (Configuración inicial)
db_config = SiteConfig(
    company_name="Brayan Brush",
    logo_text="Corporación Logística",
    hero_title="Brayan Brush.",
    hero_subtitle="Líder en transporte terrestre nacional en Perú.",
    primary_color="#059669",
    banner_bg_url=None
)

def _full_upload_url(path: str) -> str:
    base = settings.API_BASE_URL.rstrip("/")
    p = path if path.startswith("/") else f"/{path}"
    return f"{base}{p}"

def _config_from_db():
    if not database.USE_MYSQL or not database.SessionLocal or database.SiteConfigModel is None:
        return None
    with database.get_db_session() as session:
        if not session:
            return None
        row = session.query(database.SiteConfigModel).filter_by(id="default").first()
        if not row:
            return None
        return SiteConfig(
            company_name=row.company_name,
            logo_text=row.logo_text,
            hero_title=row.hero_title,
            hero_subtitle=row.hero_subtitle,
            primary_color=row.primary_color,
            logo_url=row.logo_url,
            banner_url=row.banner_url,
            banner_bg_url=row.banner_bg_url,
        )

def _save_config_to_db(c: SiteConfig):
    if not database.USE_MYSQL or not database.SessionLocal or database.SiteConfigModel is None:
        return
    with database.get_db_session() as session:
        if not session:
            return
        row = session.query(database.SiteConfigModel).filter_by(id="default").first()
        if not row:
            row = database.SiteConfigModel(id="default")
            session.add(row)
        row.company_name = c.company_name
        row.logo_text = c.logo_text
        row.hero_title = c.hero_title
        row.hero_subtitle = c.hero_subtitle
        row.primary_color = c.primary_color
        row.logo_url = c.logo_url
        row.banner_url = c.banner_url
        row.banner_bg_url = c.banner_bg_url

@app.get("/config")
def get_config():
    global db_config
    from_db = _config_from_db()
    if from_db is not None:
        db_config = from_db
    return db_config

@app.post("/config/upload-logo")
async def upload_logo(file: UploadFile = File(...)):
    ext = file.filename.split(".")[-1].lower()
    if ext not in settings.ALLOWED_EXTENSIONS:
        raise HTTPException(status_code=400, detail="Formato de archivo no permitido")
    
    file_path = os.path.join(settings.UPLOAD_DIR, f"logo_{int(datetime.now().timestamp())}.{ext}")
    with open(file_path, "wb") as buffer:
        shutil.copyfileobj(file.file, buffer)
    
    db_config.logo_url = _full_upload_url(f"/uploads/{os.path.basename(file_path)}")
    return {"url": db_config.logo_url}

@app.post("/config/upload-banner")
async def upload_banner(file: UploadFile = File(...)):
    ext = file.filename.split(".")[-1].lower()
    if ext not in settings.ALLOWED_EXTENSIONS:
        raise HTTPException(status_code=400, detail="Formato de archivo no permitido")
    
    file_path = os.path.join(settings.UPLOAD_DIR, f"banner_{int(datetime.now().timestamp())}.{ext}")
    with open(file_path, "wb") as buffer:
        shutil.copyfileobj(file.file, buffer)
    
    db_config.banner_url = _full_upload_url(f"/uploads/{os.path.basename(file_path)}")
    return {"url": db_config.banner_url}

@app.post("/config/upload-banner-bg")
async def upload_banner_bg(file: UploadFile = File(...)):
    ext = file.filename.split(".")[-1].lower()
    if ext not in settings.ALLOWED_EXTENSIONS:
        raise HTTPException(status_code=400, detail="Formato de archivo no permitido")
    
    file_path = os.path.join(settings.UPLOAD_DIR, f"banner_bg_{int(datetime.now().timestamp())}.{ext}")
    with open(file_path, "wb") as buffer:
        shutil.copyfileobj(file.file, buffer)
    
    db_config.banner_bg_url = _full_upload_url(f"/uploads/{os.path.basename(file_path)}")
    return {"url": db_config.banner_bg_url}

@app.post("/config")
def update_config(config: SiteConfig):
    global db_config
    db_config = config
    _save_config_to_db(config)
    return {"message": "Configuración actualizada"}


@app.get("/health")
def health():
    """Estado de la API y de la base de datos."""
    return {
        "status": "ok",
        "mysql": database.USE_MYSQL,
    }


# --- Asistente IA (Gemini en backend; la API key no se expone al frontend) ---
class AssistantRequest(BaseModel):
    message: str

class AssistantResponse(BaseModel):
    text: str

@app.post("/assistant/chat", response_model=AssistantResponse)
async def assistant_chat(body: AssistantRequest):
    if not _gemini_model:
        raise HTTPException(
            status_code=503,
            detail="Asistente no configurado. Definir GEMINI_API_KEY en el servidor.",
        )
    try:
        response = _gemini_model.generate_content(body.message)
        text = response.text if response.text else "No pude generar una respuesta."
        return AssistantResponse(text=text)
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Error del asistente: {str(e)}")
