from ultralytics import YOLO

if __name__ == "__main__":
    data_path = r"C:\xampp\htdocs\SIMAR\ia\dataset\Simar.v4-mon-1.3.yolov11\data.yaml"

    # yolo11n.pt SIN combinar con Mon1.0.pt ni ningún checkpoint anterior
    model = YOLO("yolo11n.pt")

    model.train(
        data=data_path,
        epochs=150,
        imgsz=640,
        batch=16,
        device=0,
        project=r"C:\xampp\htdocs\SIMAR\ia\resultados",
        name="entrenamiento_5",
        patience=30,
        close_mosaic=10,
        cos_lr=True,
    )