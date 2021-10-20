package com.delaroystudios.camera;

import android.app.Dialog;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Camera;
import android.graphics.Typeface;
import android.os.Bundle;
import android.support.v4.app.ActivityCompat;
import android.support.v7.app.ActionBarActivity;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;

import org.opencv.android.OpenCVLoader;

import static android.R.id.message;
import static android.provider.AlarmClock.EXTRA_MESSAGE;


public class MainActivity extends ActionBarActivity {

    static {
        if (!OpenCVLoader.initDebug()) {
            Log.d("MainActivity", "OpenCV not loader");
        } else {
            Log.d("MainActivity", "OpenCV loader");
        }
        System.loadLibrary("native-lib");
    }


    Dialog dialog;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.menu_layout);
    }

    public void playApplication(View view) {
        setContentView(R.layout.config_layout);
        dialog = new Dialog(this);
        dialog.setContentView(R.layout.config_layout);
        dialog.setTitle("Error message");
        dialog.show();
    }

    public void endApplication(View view) {
        System.exit(0);
    }

    public void insertCalibrationData(View view) {
        String regexStr = "^[0-9]*$";
        EditText enter_focal_length = (EditText) dialog.findViewById(R.id.enter_focal_length);
        EditText enter_optical_center = (EditText) dialog.findViewById(R.id.enter_optical_center);
        String focal_length = enter_focal_length.getText().toString();
        String optical_center = enter_optical_center.getText().toString();

        if (!focal_length.trim().matches(regexStr)) {
            TextView error_message = (TextView) dialog.findViewById(R.id.error_message);
            error_message.setVisibility(View.VISIBLE);
        } else if (!optical_center.trim().matches(regexStr)) {
            TextView error_message = (TextView) dialog.findViewById(R.id.error_message);
            error_message.setVisibility(View.VISIBLE);
        } else {
            ActivityCompat.finishAffinity(this);
            Intent intent = new Intent(this, CameraActivity.class);
            intent.putExtra(EXTRA_MESSAGE, focal_length + ";" + optical_center);
            startActivity(intent);
        }
    }
}
