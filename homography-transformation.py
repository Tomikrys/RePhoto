import numpy as np
import cv2
import sys
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

    M, mask = cv2.findHomography(src_pts, dst_pts, cv2.RANSAC,5.0)
    matchesMask = mask.ravel().tolist()

    im_out = cv2.warpPerspective(img2, M, (img1.shape[1],img1.shape[0]))
    cv2.imwrite(sys.argv[3], im_out);
    print('true')
else:
    print('false')