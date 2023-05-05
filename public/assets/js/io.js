class IO {

  constructor(socket){
    if(!IO.instance){
      this.socket = socket;
      this.token = Token.generateToken(64);

      IO.instance = this;
    }

    return IO.instance;
  }

  join(chanel) {
    this.socket.emit('join', chanel);
  }

}