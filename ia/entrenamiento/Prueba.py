import cv2
from ultralytics import YOLO

weights = r"C:\xampp\htdocs\SIMAR\ia\resultados\entrenamiento_5\weights\best.pt"

model = YOLO(weights)
print("Clases del modelo:", model.names)  # confirma que cargó las clases bien

cap = cv2.VideoCapture(0)

while True:
    ret, frame = cap.read()

    if not ret:
        print("No se pudo acceder a la cámara")
        break

    # conf bajo para probar si detecta algo, aunque sea débil
    results = model(frame, conf=0.5, iou=0.5, verbose=False)

    print("Detecciones:", len(results[0].boxes))  # cuántas cajas encontró

    annotated_frame = results[0].plot()
    cv2.imshow("SIMAR - Deteccion de productos", annotated_frame)

    if cv2.waitKey(1) & 0xFF == ord("q"):
        break

cap.release()
cv2.destroyAllWindows()