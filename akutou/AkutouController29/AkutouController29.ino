#include <SoftPWM.h>
#include <avr/pgmspace.h>
#define REP(i,n) for(int i=0;i<(n);i++)
#define SIZE_OF(x) sizeof(x) / sizeof(x[0])

/***** Arduino Nano pin setting *****/
/* Interrupt pin */
#define INT_SW 2
/* East-West switch */
#define DIR_SW 19 // LOW for West, HIGH for East
/* for 7 segment driver 74HC4511 */
#define SEG0 18
#define SEG1 4
#define SEG2 3
#define SEG3 17
/* PWM pins */
#define PWM_W0 1
#define PWM_W1 5
#define PWM_W2 6
#define PWM_W3 7
#define PWM_W4 8
#define PWM_E0 13
#define PWM_E1 9
#define PWM_E2 10
#define PWM_E3 11
#define PWM_E4 12
/* Analog pins */
#define VR1 0 // A0(master volume)
#define VR2 1 // A1
#define VR3 2 // A2
#define VR4 6 // A6(analog only)
#define VR5 7 // A7(analog only)
/************************************/

const int PWM_Pins[10] = {PWM_W0, PWM_W1, PWM_W2, PWM_W3, PWM_W4, PWM_E0, PWM_E1, PWM_E2, PWM_E3, PWM_E4};
const int VolumeToPin[5][3] = {
  {VR1, PWM_W0, PWM_E0},
  {VR2, PWM_W1, PWM_E1},
  {VR3, PWM_W2, PWM_E2},
  {VR4, PWM_W3, PWM_E3},
  {VR5, PWM_W4, PWM_E4},
};
const int FadeTime = 100; // Time in milliseconds of 0 -> 255 or 255 -> 0
PROGMEM const unsigned char LightPattern[3][5][120] = { // put user setting pattern here
  {/* leave here blank for Auto mode! */},
  {
    {0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,4,8,11,15,19,23,26,30,34,38,41,45,49,53,56,60,64,68,71,75,79,83,86,90,94,98,101,105,109,113,113,107,101,95,88,82,76,69,63,57,50,44,38,32,25,19,13,0,0},
    {0,0,6,8,11,14,17,20,22,25,28,31,34,36,39,42,45,48,50,53,56,59,62,64,67,70,73,76,78,81,84,87,90,92,95,98,99,95,90,86,82,78,74,69,65,61,57,53,48,44,40,36,32,27,23,19,15,11,6,2,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0},
    {0,0,11,17,22,28,34,39,45,50,56,62,67,73,78,84,90,95,101,106,112,118,123,129,134,140,146,151,157,162,168,174,179,185,190,196,199,195,190,186,182,178,174,169,165,161,157,153,148,144,140,136,132,127,123,119,115,111,106,102,98,94,89,85,81,77,73,68,64,60,56,52,47,43,39,35,31,26,22,18,14,10,5,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0},
    {0,0,0,0,0,0,0,0,0,0,0,0,1,8,15,22,29,36,44,51,58,65,72,79,86,94,101,108,115,122,129,136,144,151,158,165,172,179,186,194,201,208,215,222,229,236,244,251,253,249,245,240,236,232,228,223,219,215,210,206,202,198,193,189,185,180,176,172,168,163,159,155,150,146,142,138,133,129,125,120,116,112,108,103,99,95,90,86,82,78,73,69,65,60,56,52,48,43,39,35,30,26,22,18,13,9,5,0,0,0,0,0,0,0,0,0,0,0,0,0},
    {0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,3,9,15,21,28,34,40,47,53,59,66,72,78,84,91,97,103,110,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,113,107,101,95,88,82,76,69,63,57,50,44,38,32,25,19,13,0,0}
  },
  {
    {0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0},
    {0,0,4,6,8,11,13,15,17,19,21,23,25,27,29,32,34,36,38,40,42,44,46,48,50,49,49,48,48,47,47,46,46,45,45,44,44,43,43,42,42,41,41,40,39,39,38,38,37,37,36,36,35,35,34,34,33,33,32,32,31,31,30,29,29,28,28,27,27,26,26,25,25,24,24,23,23,22,22,21,21,20,19,19,18,18,17,17,16,16,15,15,14,14,13,13,12,12,11,11,10,9,9,8,8,7,7,6,6,5,5,4,4,3,3,2,2,1,0,0},
    {0,0,8,13,17,21,25,29,34,38,42,46,50,55,59,63,67,71,76,80,84,88,92,97,101,105,109,113,118,122,126,130,134,139,143,147,151,155,160,164,168,172,176,181,185,189,193,197,200,192,187,181,175,170,164,159,153,147,142,136,131,125,119,114,108,103,97,91,86,80,75,69,63,58,52,46,41,35,30,24,18,13,7,2,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0},
    {0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,7,12,18,24,29,35,40,46,52,57,63,68,74,80,85,91,96,102,108,113,119,124,130,136,141,147,152,158,164,169,175,180,186,192,197,198,195,192,188,185,182,178,175,171,168,165,161,158,155,151,148,145,141,138,134,131,128,124,121,118,114,111,108,104,101,97,94,91,87,84,81,77,74,71,67,64,61,57,54,50,47,44,40,37,34,30,27,24,20,17,13,10,7,0,0},
    {0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,2,4,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36,38,40,42,44,46,48,50,48,46,44,42,40,38,36,34,32,30,28,26,24,22,20,18,16,14,12,10,8,6,4,2,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0}
  }
}; // light controll pattern(max 255)
volatile int mode_flag = 0; // save current mode number
volatile unsigned long time_pre = 0, time_cur; // avoid chattering

struct HC4511{
  int pin[4];
  HC4511(int p0, int p1, int p2, int p3): pin{p0,p1,p2,p3}{
    REP(i,4){
      pinMode(pin[i], OUTPUT);
      digitalWrite(pin[i], HIGH); // all HIGH for blank
    }
  }
  void write(int value){
      REP(i,4){
        digitalWrite(pin[i], value % 2);
        value /= 2;
      }
  }
  HC4511& operator = (int value){write(value);return *this;}
};

HC4511 segLED(SEG0, SEG1, SEG2, SEG3);

void modeSelect(){
  time_cur = millis();
//  Serial.print("time_cur - time_pre: ");
//  Serial.println(time_cur - time_pre);
  if(time_cur - time_pre <= 200) return;
  mode_flag = (mode_flag + 1) % 3; // mode_flag = (0, 1) < LightPattern
//  Serial.print("mode ");
//  Serial.println(mode_flag);
  time_pre = time_cur;
}

void setup(){
//  Serial.begin(9600);
//  Serial.println("setup begin");
  pinMode(INT_SW, INPUT_PULLUP);
  attachInterrupt(digitalPinToInterrupt(INT_SW), modeSelect, FALLING);
  pinMode(DIR_SW, INPUT_PULLUP);
  segLED = mode_flag;
  /* Initialize SoftPWM library */
  SoftPWMBegin(); 
  REP(i,SIZE_OF(PWM_Pins)){
    SoftPWMSet(PWM_Pins[i], 0);
    SoftPWMSetFadeTime(PWM_Pins[i], FadeTime, FadeTime);
  }
//  Serial.println("setup end");
}

void loop(){
  pinMode(INT_SW, INPUT_PULLUP);
  segLED = mode_flag;
  int direction = digitalRead(DIR_SW); // West: 0, East: 1
  if(!mode_flag){ /* Manual mode */
    REP(i,5){
      int input = analogRead(VolumeToPin[i][0]);
      SoftPWMSet(VolumeToPin[i][direction + 1], map(input, 0, 1023, 0, 255));
      SoftPWMSet(VolumeToPin[i][!direction + 1], 0);
      delayMicroseconds(100);
    }
  }
  else{ /* Auto mode */
    unsigned int input = analogRead(VR1);
    unsigned int pos = min(round(120 / 1023.0 * input), 120 - 1);
    //Serial.println(input);
    REP(i,5){
      unsigned char light = pgm_read_byte_near(&(LightPattern[mode_flag][i][pos]));
      SoftPWMSet(VolumeToPin[i][direction + 1], light);
      SoftPWMSet(VolumeToPin[i][!direction + 1], 0);
      delayMicroseconds(100);
    }
  }
}

