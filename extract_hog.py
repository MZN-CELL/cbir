import sys
import cv2
import numpy as np
from skimage.feature import hog

def extract_hog_features(image_path):
    image = cv2.imread(image_path)
    image = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    image = cv2.resize(image, (128, 64))  # Resize as needed
    features = hog(image, pixels_per_cell=(8, 8), cells_per_block=(2, 2), orientations=9, block_norm='L2-Hys')
    return features.tolist()  # Convert to list for JSON serialization

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Usage: python extract_hog.py <image_path>")
        sys.exit(1)

    image_path = sys.argv[1]
    hog_features = extract_hog_features(image_path)
    print(hog_features)  # Print the features as output
