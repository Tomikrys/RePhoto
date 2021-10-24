package com.delaroystudios.camera;

import org.opencv.core.Mat;

/**
 * Created by acervenka2 on 21.04.2017.
 */

public class OpenCVNative {

    static {
        System.loadLibrary("native-lib");
    }

    public static native void initReconstruction(long first_frame, long second_frame, long reference_frame, float cx_f, float cy_f, float fx_f, float fy_f, float cx_c, float cy_c, float fx_c, float fy_c);

    public static native void processReconstruction();

    public static native void nextPoint();

    public static native long registrationPoints(double x, double y);

    public static native void initNavigation();

    public static native int processNavigation(long current_frame, int count_frames);
}
