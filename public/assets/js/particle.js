var COLORS, NUM_BANDS, Particle, SCALE, SIZE, SMOOTHING, SPEED, SPIN;

var TWO_PI = 6.2831855;

NUM_BANDS = 128;

SMOOTHING = 0.5;

SCALE = {
  MIN: 5.0,
  MAX: 80.0
};

SPEED = {
  MIN: 0.2,
  MAX: 1.0
};

ALPHA = {
  MIN: 0.2,
  MAX: 0.9
};

SPIN = {
  MIN: 0.001,
  MAX: 0.005
};

SIZE = {
  MIN: 0.5,
  MAX: 1.25
};

COLORS = ['#69D2E7', '#1B676B', '#BEF202', '#EBE54D', '#00CDAC', '#1693A5', '#F9D423', '#FF4E50', '#E7204E', '#0canvasABA', '#FF006F'];

// Particle
Particle = class Particle {
  constructor(ctx, x1 = 0, y1 = 0) {
    this.ctx = ctx;
    this.x = x1;
    this.y = y1;
    this.reset();
  }

  reset() {
    this.level = 1 + Math.floor(Math.random()*4);
    this.scale = Math.random() * SCALE.MIN + SCALE.MAX;
    this.alpha = Math.random() * ALPHA.MIN + ALPHA.MAX;
    this.speed = Math.random() * SPEED.MIN + SPEED.MAX;
    this.color = COLORS[Math.floor(Math.random()*11)];
    this.size = Math.random() * SIZE.MIN + SIZE.MAX;
    this.spin = Math.random() * SPIN.MAX + SPIN.MAX;
    this.band = Math.floor(Math.random() * NUM_BANDS);
    if (Math.random() < 0.5) {
      this.spin = -this.spin;
    }
    this.smoothedScale = 0.0;
    this.smoothedAlpha = 0.0;
    this.decayScale = 0.0;
    this.decayAlpha = 0.0;
    this.rotation = Math.random() * TWO_PI;

    return this.energy = 0.0;
  }

  move() {
    this.rotation += this.spin;
    return this.y -= this.speed * this.level;
  }

  draw(ctx) {
    var alpha, power, scale;
    power = Math.exp(this.energy);
    scale = this.scale * power;
    alpha = this.alpha * this.energy * 1.5;
    this.decayScale = Math.max(this.decayScale, scale);
    this.decayAlpha = Math.max(this.decayAlpha, alpha);
    this.smoothedScale += (this.decayScale - this.smoothedScale) * 0.3;
    this.smoothedAlpha += (this.decayAlpha - this.smoothedAlpha) * 0.3;
    this.decayScale *= 0.985;
    this.decayAlpha *= 0.975;
    ctx.save();
    ctx.beginPath();
    ctx.translate(this.x + Math.cos(this.rotation * this.speed) * 250, this.y);
    ctx.rotate(this.rotation);
    ctx.scale(this.smoothedScale * this.level, this.smoothedScale * this.level);
    ctx.moveTo(this.size * 0.5, 0);
    ctx.lineTo(this.size * -0.5, 0);
    ctx.lineWidth = 1;
    ctx.lineCap = 'round';
    ctx.globalAlpha = this.smoothedAlpha / this.level;
    ctx.strokeStyle = this.color;
    ctx.stroke();
    return ctx.restore();
  }

};