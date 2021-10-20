package com.delaroystudios.camera;

import android.app.Activity;
import android.app.ActivityManager;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Matrix;
import android.media.Image;
import android.os.Bundle;
import android.support.v4.app.ActivityCompat;
import android.support.v7.app.ActionBarActivity;
import android.view.Menu;
import android.view.MenuItem;
import android.view.MotionEvent;
import android.widget.ImageView;
import android.widget.Toast;

import org.opencv.android.Utils;
import org.opencv.core.Mat;

import static java.security.AccessController.getContext;

/**
 * Created by acervenka2 on 20.03.2017.
 */

public class SelectPointsActivity extends ActionBarActivity {


    Bitmap ref_frame, first_frame;
    private float x1, x2;
    static final int MIN_DISTANCE = 100;
    ImageView imageView;

    boolean isRefImage;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.select_points);

        imageView = (ImageView) findViewById(R.id.imageView);

        isRefImage = true;


        long frameAddress = getIntent().getLongExtra("frame", 0);
        Mat out = new Mat(frameAddress);

       /* Bitmap.Config conf = Bitmap.Config.ARGB_4444;
        first_frame = Bitmap.createBitmap(out.width(), out.height(), conf);
        Utils.matToBitmap(out, first_frame);*/
        first_frame = BitmapFactory.decodeResource(getResources(), R.drawable.ref_biskupsky_palac);
        ref_frame = BitmapFactory.decodeResource(getResources(), R.drawable.ref_biskupsky_palac);
    }

    @Override
    public boolean onTouchEvent(MotionEvent event) {
        return super.onTouchEvent(event);
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.change_view, menu);
        return true;
    }

    public void changeView(MenuItem item) {
        if (!isRefImage) {
            imageView.setImageBitmap(ref_frame);
            isRefImage = true;
        } else {
            imageView.setImageBitmap(first_frame);
            isRefImage = false;
        }
    }

    public void finishRegister(MenuItem item) {
        ActivityCompat.finishAffinity(this);
        Intent play = new Intent(SelectPointsActivity.this, MyRealTimeImageProcessing.class);
        startActivity(play);
    }

    public void exitApplication(MenuItem item) {
        System.exit(0);
    }
}
