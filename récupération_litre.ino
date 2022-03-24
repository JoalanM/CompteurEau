//Parameters
const int didPin  = 4;

//Variables
bool didStatus  = false;
bool oldDidStatus  = false;
unsigned long lastDebounceTime  = 0;
unsigned long debounceDelay  = 50;
int litre= 0 ;

void setup() {
  //Init Serial USB
  Serial.begin(115200);
  Serial.println(F("Initialize System"));
  //Init digital input
  pinMode(didPin, INPUT_PULLUP);
}

void loop() {
  debounceDid();
}

void debounceDid( ) { /* function debounceDid */
  ////debounce DigitalDebounce
  int reading = digitalRead(didPin);
  if (reading != oldDidStatus) {
    lastDebounceTime = millis();
  }
  if ((millis() - lastDebounceTime)>= debounceDelay) {
    if (reading != didStatus) {
      didStatus = reading;
      
      if(didStatus == 1){
        litre++;
      Serial.print(litre); Serial.println("litres");
      }
      Serial.print(F("Sensor state : ")); Serial.println(didStatus);
    }
  }
  oldDidStatus = reading;
}
