import cv2
from ultralytics import YOLO
import os

BASE = os.path.dirname(__file__)

weights = os.path.join(BASE, "resultados_S.I.M.A.Rv1", "entrenamiento_3", "weights", "best.pt")
model = YOLO(weights)

cap = cv2.VideoCapture(1)

while True:
    ret, frame = cap.read()
    if not ret:
        break

    
    results = model(frame)

    #
    annotated_frame = results[0].plot()

   
    cv2.imshow("YOLOv8 Cam", annotated_frame)  
  
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()
