#print('{"new": "[[[803.0, 245.0]], [[102.0, 891.0]], [[102.0, 891.0]], [[660.0, 837.0]], [[1030.0, 183.0]], [[882.0, 306.0]], [[882.0, 306.0]], [[1031.0, 352.0]], [[933.0, 820.0]], [[1033.0, 383.0]], [[1035.0, 388.0]], [[1047.0, 394.0]], [[1042.0, 401.0]], [[1024.0, 415.0]], [[1030.0, 449.0]], [[1026.0, 453.0]], [[1057.0, 411.0]], [[243.0, 715.0]], [[948.0, 1097.0]], [[53.0, 1136.0]], [[149.0, 1137.0]], [[57.0, 1138.0]], [[51.0, 1140.0]]]", "old": "[[[601.0, 39.0]], [[1118.0, 95.0]], [[1115.0, 97.0]], [[521.0, 138.0]], [[1070.0, 151.0]], [[881.0, 309.0]], [[880.0, 312.0]], [[1031.0, 351.0]], [[520.0, 375.0]], [[1029.0, 381.0]], [[1032.0, 387.0]], [[1045.0, 393.0]], [[1039.0, 399.0]], [[1019.0, 412.0]], [[1025.0, 446.0]], [[1022.0, 450.0]], [[600.0, 711.0]], [[581.0, 792.0]], [[1210.0, 988.0]], [[53.0, 1136.0]], [[149.0, 1137.0]], [[56.0, 1138.0]], [[51.0, 1140.0]]]"}')
#exit()

import numpy as np
import cv2
import sys
import json

#from matplotlib import pyplot as plt

MIN_MATCH_COUNT = 10

if (len(sys.argv) != 4):
    print('2 arguments required');
    exit()

img1 = cv2.imread(sys.argv[1])
img2 = cv2.imread(sys.argv[2])

# Initiate FAST object with default values
fast = cv2.FastFeatureDetector_create()
# find and draw the keypoints
kp1 = fast.detect(img1,None)
kp2 = fast.detect(img2,None)

orb = cv2.ORB_create()
kp1, des1 = orb.compute(img1, kp1)
kp2, des2 = orb.compute(img2, kp2)

# filter descriptors
FLANN_INDEX_KDTREE = 0
FLANN_INDEX_LSH = 6
#index_params = dict(algorithm = FLANN_INDEX_KDTREE, trees = 5)
index_params= dict(algorithm = FLANN_INDEX_LSH,
                   table_number = 6, # 12
                   key_size = 12,     # 20
                   multi_probe_level = 1) #2


search_params = dict(checks = 50)

flann = cv2.FlannBasedMatcher(index_params, search_params)

matches = flann.knnMatch(des1,des2,k=2)

# store all the good matches as per Lowe's ratio test.
good = []
for m,n in matches:
    if m.distance < 0.7*n.distance:
        good.append(m)

if len(good)>MIN_MATCH_COUNT:
    src_pts = np.float32([ kp1[m.queryIdx].pt for m in good ]).reshape(-1,1,2)
    dst_pts = np.float32([ kp2[m.trainIdx].pt for m in good ]).reshape(-1,1,2)

    print({"old": json.dumps(src_pts.tolist()), "new": json.dumps(dst_pts.tolist())})
else:
    print('false')