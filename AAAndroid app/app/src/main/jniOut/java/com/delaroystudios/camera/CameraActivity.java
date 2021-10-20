package com.delaroystudios.camera;

import android.content.ActivityNotFoundException;
import android.content.Context;
import android.content.Intent;
import android.content.res.Resources;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Canvas;
import android.graphics.Typeface;
import android.graphics.drawable.Drawable;
import android.net.Uri;
import android.os.Bundle;
import android.os.Environment;
import android.support.v4.app.ActivityCompat;
import android.support.v7.app.ActionBarActivity;
import android.support.v7.app.AppCompatActivity;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.SurfaceHolder;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.RelativeLayout;

import org.opencv.android.Utils;
import org.opencv.core.Mat;

import java.io.Serializable;
import java.util.ArrayList;

import static android.provider.AlarmClock.EXTRA_MESSAGE;

/**
 * Created by acervenka2 on 20.03.2017.
 */

public class CameraActivity extends ActionBarActivity {

    private static String TAG = "CameraActivity";

    private ImageView capturedImage;

    private ArrayList<Bitmap> images;

    private double focal_length;
    private double optical_center;

    private Mat firstFrame = new Mat();
    private Mat secondFrame = new Mat();
    private Mat refFrame = new Mat();

    private float cx_f, cy_f, fx_f, fy_f, cx_c, cy_c, fx_c, fy_c;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        ImageView layout1 = (ImageView) findViewById(R.id.capturedImage);
        Drawable myDrawable = getResources().getDrawable(R.drawable.rsz_biskupsky_palac_4);
        layout1.setImageDrawable(myDrawable);


        Typeface font = Typeface.createFromAsset( getAssets(), "fontawesome-webfont.ttf" );
        Button btnCamera = (Button) findViewById(R.id.btnCamera);

        capturedImage = (ImageView) findViewById(R.id.capturedImage);

        btnCamera.setTypeface(font);

        btnCamera.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                openCamera();
            }
        });

        Intent intent = getIntent();
        String message = intent.getStringExtra(EXTRA_MESSAGE);
        String delims = "[;]";
        String[] value = message.split(delims);
        cx_f = Float.parseFloat(value[0]);
        cy_f = Float.parseFloat(value[1]);
        fx_f = Float.parseFloat(value[2]);
        fy_f = Float.parseFloat(value[3]);

        cx_c = Float.parseFloat(value[4]);
        cy_c = Float.parseFloat(value[5]);
        fx_c = Float.parseFloat(value[6]);
        fy_c = Float.parseFloat(value[7]);


        images = new ArrayList<>();
    }

    private void openCamera() {
        Intent intent = new Intent(android.provider.MediaStore.ACTION_IMAGE_CAPTURE);
        startActivityForResult(intent, 0);
    }


    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        Log.d(TAG, "Some data");

        if(resultCode == RESULT_OK) {
            Bitmap bp = (Bitmap) data.getExtras().get("data");
            capturedImage.setImageBitmap(bp);
            images.add(bp);
            if(images.size() == 2){

                Utils.bitmapToMat(BitmapFactory.decodeResource(getResources(), R.drawable.biskupsky_palac2), firstFrame);
                Utils.bitmapToMat(BitmapFactory.decodeResource(getResources(), R.drawable.biskupsky_palac3), secondFrame);
                Utils.bitmapToMat(BitmapFactory.decodeResource(getResources(), R.drawable.ref_biskupsky_palac), refFrame);

                Log.d("CameraActivity", "Now procesesing native method");
                OpenCVNative.initReconstruction(firstFrame.getNativeObjAddr(), secondFrame.getNativeObjAddr(), refFrame.getNativeObjAddr(),
                cx_f, cy_f, fx_f, fy_f, cx_c, cy_c, fx_c, fy_c);

                ActivityCompat.finishAffinity(this);
                Intent intent = new Intent(this, SelectPointsActivity.class);
                startActivity(intent);
            }
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.menu_main, menu);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        int id = item.getItemId();
        return id == R.id.action_settings || super.onOptionsItemSelected(item);

    }

    public void exitApplication(MenuItem item) {
        System.exit(0);
    }
}
