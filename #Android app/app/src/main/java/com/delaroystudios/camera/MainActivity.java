package com.delaroystudios.camera;

import android.app.Dialog;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.ActivityCompat;
import android.support.v7.app.ActionBarActivity;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.EditText;
import android.widget.TextView;

import org.opencv.android.OpenCVLoader;

import static android.provider.AlarmClock.EXTRA_MESSAGE;


public class MainActivity extends ActionBarActivity {

    private static String TAG = "MainActivity";

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

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        getMenuInflater().inflate(R.menu.menu_main, menu);
        return true;
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
        EditText tf[] = new EditText[8];
        String params[] = new String[8];
        String message = "";
        boolean isCorrect = true;

        tf[0] = (EditText) dialog.findViewById(R.id.tf_cxf);
        tf[1] = (EditText) dialog.findViewById(R.id.tf_cyf);
        tf[2] = (EditText) dialog.findViewById(R.id.tf_fxf);
        tf[3] = (EditText) dialog.findViewById(R.id.tf_fyf);
        tf[4] = (EditText) dialog.findViewById(R.id.tf_cxc);
        tf[5] = (EditText) dialog.findViewById(R.id.tf_cyc);
        tf[6] = (EditText) dialog.findViewById(R.id.tf_fxc);
        tf[7] = (EditText) dialog.findViewById(R.id.tf_fyc);

        for(int i = 0; i < tf.length; i++){
            params[i] = tf[i].getText().toString();
            message += params[i] + ";";
            if (!params[i].trim().matches(regexStr)) {
                TextView error_message = (TextView) dialog.findViewById(R.id.error_message);
                error_message.setVisibility(View.VISIBLE);
                isCorrect = false;
                break;
            }
        }

        if(isCorrect){
            ActivityCompat.finishAffinity(this);
            Intent intent = new Intent(this, SettingActivity.class);
            intent.putExtra(EXTRA_MESSAGE, message);
            startActivity(intent);
            finish();
        }
    }

    public void exitApplication(MenuItem item) {
        finish();
    }
}
