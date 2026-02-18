"""
Capa de persistencia MySQL para Brayan Brush.
Solo se usa si DB_PASSWORD (o USE_MYSQL=1) está definido y la conexión funciona.
"""
import os
from contextlib import contextmanager

from .config import settings

# Solo intentar MySQL si hay contraseña (evitar fallos en entornos sin BD)
USE_MYSQL = os.getenv("USE_MYSQL", "0") == "1" or bool(settings.DB_PASSWORD)
engine = None
SessionLocal = None
Base = None
SiteConfigModel = None

if USE_MYSQL:
    try:
        from sqlalchemy import create_engine, Column, String, Text
        from sqlalchemy.orm import sessionmaker, declarative_base

        Base = declarative_base()

        class SiteConfigModel(Base):  # type: ignore[valid-type,misc]
            __tablename__ = "site_config"
            id = Column(String(32), primary_key=True, default="default")
            company_name = Column(String(255), nullable=False)
            logo_text = Column(String(255), nullable=False)
            hero_title = Column(String(255), nullable=False)
            hero_subtitle = Column(String(500), nullable=False)
            primary_color = Column(String(20), nullable=False)
            logo_url = Column(Text, nullable=True)
            banner_url = Column(Text, nullable=True)
            banner_bg_url = Column(Text, nullable=True)

        engine = create_engine(
            settings.DATABASE_URL,
            pool_pre_ping=True,
            echo=os.getenv("SQL_ECHO", "0") == "1",
        )
        SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
        Base.metadata.create_all(bind=engine)
    except Exception as e:
        USE_MYSQL = False
        engine = None
        SessionLocal = None
        SiteConfigModel = None
        import warnings
        warnings.warn("MySQL no disponible: %s. Se usará configuración en memoria." % e)


@contextmanager
def get_db_session():
    if not SessionLocal:
        yield None
        return
    session = SessionLocal()
    try:
        yield session
        session.commit()
    except Exception:
        session.rollback()
        raise
    finally:
        session.close()
