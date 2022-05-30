import numpy as np
import cv2
import sys
import json
import os


if (len(sys.argv) != 5):
    print('2 arguments required')
    exit()

img1 = cv2.imread(sys.argv[1])
img2 = cv2.imread(sys.argv[2])

file = open(sys.argv[4], "r")
points = json.loads(file.read())

dst_pts = np.float32(points['old']).reshape(-1,1,2)
src_pts = np.float32(points['new']).reshape(-1,1,2)
print (src_pts)
print (dst_pts)

h1, w1, c = img1.shape
h2, w2, c = img2.shape

# scale the dst_point to resolution of the image we will be warping
if (h1 < h2 and w1 < w2):
    for i in range(len(dst_pts)):
        print(dst_pts[i][0])
        print((float(w2)/w1))
        dst_pts[i][0][0] = dst_pts[i][0][0] * (float(w2)/w1)
        dst_pts[i][0][1] = dst_pts[i][0][1] * (float(h2)/h1) 
        print(dst_pts[i][0])
    

M, mask = cv2.findHomography(src_pts, dst_pts, cv2.RANSAC, 5.0)
print(M)
# if (h1 < h2 and w1 < w2):
# if (False):
#     M = np.multiply(M, [ [(float(w2)/w1), 1, 1], [1, (float(h2)/h1), 1], [1, 1, 1,] ])
#     print(M)
#     print(w2, w1)
#     print((float(w2)/float(w1)))
#     im_out = cv2.warpPerspective(img2, M, (img2.shape[1],img2.shape[0]))
#     if not os.path.exists(os.path.dirname(sys.argv[3])):
#         os.mkdir(os.path.dirname(sys.argv[3]))
#     cv2.imwrite(sys.argv[3], im_out)
#     print('true')

# else: 

im_out = cv2.warpPerspective(img2, M, (img2.shape[1],img2.shape[0]))
if not os.path.exists(os.path.dirname(sys.argv[3])):
    os.mkdir(os.path.dirname(sys.argv[3]))
cv2.imwrite(sys.argv[3], im_out)
print('true')

    
# matchesMask = mask.ravel().tolist()


# sift = cv2.SIFT()
# kp1, des1 = sift.detectAndCompute(img1,None)
# kp2, des2 = sift.detectAndCompute(img2,None)

# h = img1.shape[0]
# w = img1.shape[1]
# pts = np.float32([ [0,0],[0,h-1],[w-1,h-1],[w-1,0] ]).reshape(-1,1,2)
# dst = cv2.perspectiveTransform(pts,M)

# img2 = cv2.polylines(img2,[np.int32(dst)],True,255,3, cv2.LINE_AA)
# draw_params = dict(matchColor = (0,255,0), # draw matches in green color
#                    singlePointColor = None,
#                    matchesMask = matchesMask, # draw only inliers
#                    flags = 2)

# img3 = cv2.drawMatches(img1,kp1,img2,kp2,good,None,**draw_params)

# cv2.imwrite(sys.argv[3].jpg, img3)