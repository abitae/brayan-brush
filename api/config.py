import os
from pathlib import Path

# Cargar .env desde la raíz del proyecto (donde está .env)
_root = Path(__file__).resolve().parent.parent
_env_file = _root / ".env"
if _env_file.exists():
    try:
        from dotenv import load_dotenv
        load_dotenv(_env_file)
    except ImportError:
        pass

class Settings:
    # API base (para CORS y URLs de upload en respuestas)
    API_BASE_URL = os.getenv("API_BASE_URL", "http://localhost:8000")

    # Gemini (asistente en backend; no exponer en frontend)
    GEMINI_API_KEY = os.getenv("GEMINI_API_KEY", "")

    # Configuración de Base de Datos MySQL
    DB_USER = os.getenv("DB_USER", "root")
    DB_PASSWORD = os.getenv("DB_PASSWORD", "")  # Usar variable de entorno en producción
    DB_HOST = os.getenv("DB_HOST", "localhost")
    DB_PORT = os.getenv("DB_PORT", "3306")
    DB_NAME = os.getenv("DB_NAME", "brayan_brush_db")

    DATABASE_URL = f"mysql+pymysql://{DB_USER}:{DB_PASSWORD}@{DB_HOST}:{DB_PORT}/{DB_NAME}"

    # Configuración de Archivos (ruta absoluta para que funcione desde cualquier cwd)
    UPLOAD_DIR = str(_root / "uploads")
    ALLOWED_EXTENSIONS = {"png", "jpg", "jpeg", "webp"}

settings = Settings()
