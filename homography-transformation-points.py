import numpy as np
import cv2
import sys
import json

if (len(sys.argv) != 5):
    print('2 arguments required');
    exit()

img1 = cv2.imread(sys.argv[1])
img2 = cv2.imread(sys.argv[2])

file = open(sys.argv[4], "r")
points = json.loads(file.read())

src_pts = np.float32(points['old']).reshape(-1,1,2)
dst_pts = np.float32(points['new']).reshape(-1,1,2)

M, mask = cv2.findHomography(src_pts, dst_pts, cv2.RANSAC,5.0)
matchesMask = mask.ravel().tolist()

im_out = cv2.warpPerspective(img2, M, (img1.shape[1],img1.shape[0]))
cv2.imwrite(sys.argv[3], im_out);
print('true')
