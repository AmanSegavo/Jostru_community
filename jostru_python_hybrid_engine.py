from fastapi import FastAPI, Depends, HTTPException, Security
from fastapi.security.api_key import APIKeyHeader
from sqlalchemy import create_engine, Column, Integer, String, Float, ForeignKey, DateTime, Enum as SQLEnum
from sqlalchemy.orm import declarative_base, sessionmaker, relationship, Session
from pydantic import BaseModel
from typing import List, Optional
from datetime import datetime
import enum
import uvicorn

# ==========================================
# 1. KONFIGURASI DATABASE (SQLAlchemy)
# ==========================================
SQLALCHEMY_DATABASE_URL = "sqlite:///./jostru_python_data.db"
engine = create_engine(SQLALCHEMY_DATABASE_URL, connect_args={"check_same_thread": False})
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

# ==========================================
# 2. SISTEM KEAMANAN (API Key)
# ==========================================
API_KEY = "rahasia-super-jostru-123"
api_key_header = APIKeyHeader(name="X-API-Key", auto_error=False)

def verify_api_key(api_key: str = Security(api_key_header)):
    if api_key == API_KEY:
        return api_key
    raise HTTPException(status_code=403, detail="Akses Ditolak: API Key tidak valid")

# ==========================================
# 3. MODEL DATABASE (V1.0 Limbah & V1.2 Produksi)
# ==========================================
class DepositStatus(str, enum.Enum):
    PENDING = "PENDING"
    PROCESSED = "PROCESSED"
    REJECTED = "REJECTED"

class WasteDepositDB(Base):
    __tablename__ = "waste_deposits"
    id = Column(Integer, primary_key=True, index=True)
    user_id = Column(Integer, index=True) 
    category = Column(String, index=True) 
    weight_kg = Column(Float, nullable=False)
    status = Column(String, default=DepositStatus.PENDING.value)
    submitted_at = Column(DateTime, default=datetime.utcnow)
    production_batch = relationship("ProductionBatchDB", back_populates="source_waste", uselist=False)

class ProductionBatchDB(Base):
    __tablename__ = "production_batches"
    id = Column(Integer, primary_key=True, index=True)
    product_sku = Column(String, index=True)
    quantity_produced = Column(Float, nullable=False)
    source_waste_id = Column(Integer, ForeignKey("waste_deposits.id"), nullable=True)
    source_waste = relationship("WasteDepositDB", back_populates="production_batch")
    produced_at = Column(DateTime, default=datetime.utcnow)

Base.metadata.create_all(bind=engine)

# ==========================================
# 4. SKEMA VALIDASI DATA (Pydantic)
# ==========================================
class WasteDepositCreate(BaseModel):
    user_id: int
    category: str
    weight_kg: float

class WasteDepositResponse(WasteDepositCreate):
    id: int
    status: str
    submitted_at: datetime
    class Config:
        orm_mode = True

class ProductionBatchCreate(BaseModel):
    product_sku: str
    quantity_produced: float
    source_waste_id: Optional[int] = None

class ProductionBatchResponse(ProductionBatchCreate):
    id: int
    produced_at: datetime
    class Config:
        orm_mode = True

# ==========================================
# 5. INISIALISASI APLIKASI FASTAPI
# ==========================================
app = FastAPI(
    title="Jostru Hybrid Engine API",
    description="API Eksternal Pemroses Data Berat (Limbah & Produksi) untuk dipanggil oleh Laravel",
    version="1.2.0"
)

@app.get("/")
def root():
    return {"message": "Jostru Hybrid Engine is Online. Buka /docs untuk test API."}

# --- MODULE: V1.0 WASTE MANAGEMENT ---
@app.post("/api/v1/waste/deposits", response_model=WasteDepositResponse, tags=["V1.0 - Limbah"])
def submit_waste(deposit: WasteDepositCreate, db: Session = Depends(get_db), key: str = Depends(verify_api_key)):
    """Menerima data setoran limbah dari Laravel Jostru."""
    print(f"[*] Menerima Laporan Limbah: User {deposit.user_id}, {deposit.weight_kg}kg {deposit.category}")
    db_deposit = WasteDepositDB(**deposit.dict())
    db.add(db_deposit)
    db.commit()
    db.refresh(db_deposit)
    return db_deposit

@app.get("/api/v1/waste/deposits", response_model=List[WasteDepositResponse], tags=["V1.0 - Limbah"])
def list_pending_waste(db: Session = Depends(get_db), key: str = Depends(verify_api_key)):
    return db.query(WasteDepositDB).filter(WasteDepositDB.status == DepositStatus.PENDING.value).all()

# --- MODULE: V1.2 PRODUCTION OUTPUT ---
@app.post("/api/v1/production/process-waste", response_model=ProductionBatchResponse, tags=["V1.2 - Produksi"])
def convert_waste_to_product(waste_id: int, product_sku: str, conversion_rate: float, db: Session = Depends(get_db), key: str = Depends(verify_api_key)):
    """Mengubah limbah yang ada menjadi produk akhir."""
    waste = db.query(WasteDepositDB).filter(WasteDepositDB.id == waste_id).first()
    if not waste:
        raise HTTPException(status_code=404, detail="Data limbah tidak ditemukan")
    if waste.status != DepositStatus.PENDING.value:
        raise HTTPException(status_code=400, detail="Limbah ini sudah diproses sebelumnya")
    
    final_quantity = waste.weight_kg * conversion_rate
    new_batch = ProductionBatchDB(
        product_sku=product_sku,
        quantity_produced=final_quantity,
        source_waste_id=waste.id
    )
    db.add(new_batch)
    waste.status = DepositStatus.PROCESSED.value
    db.commit()
    db.refresh(new_batch)
    return new_batch

if __name__ == "__main__":
    print("Menjalankan Jostru Hybrid Engine pada http://0.0.0.0:8000")
    uvicorn.run("jostru_python_hybrid_engine:app", host="0.0.0.0", port=8000, reload=True)
