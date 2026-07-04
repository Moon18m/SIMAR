import os
from ultralytics import YOLO

if __name__ == "__main__":
    BASE_DIR = os.path.dirname(os.path.abspath(__file__))

    # Dataset
    data_path = os.path.join(BASE_DIR, "S.I.M.A.Rv1", "data.yaml")

    # Modelo previo (mejor versión)
    model_path = os.path.join(BASE_DIR, "resultados_S.I.M.A.Rv1", "entrenamiento_3", "weights", "best.pt")
    model = YOLO(model_path)

    # Entrenamiento
    model.train(
        data=data_path,
        epochs=80,
        imgsz=640,
        batch=16,
        device=0,
        project="resultados_S.I.M.A.Rv1",
        name="entrenamiento_3",
        patience=50
    )