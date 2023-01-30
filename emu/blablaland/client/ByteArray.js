class ByteArray extends Array {
    constructor() { super(); }
    writeByte(byte) {
        this.push(byte);
    }
    writeBoolean(bool) {
        this.writeByte(bool ? 1 : 0);
    }
    getBuffer() {
        let res = JSON.parse(JSON.stringify(this));
        let a = Buffer.from(res[0]),
            b = Buffer.from(res[1]),
            c = Buffer.from('\x00');
        return Buffer.concat([a, b, c]);
    }
}

module.exports = ByteArray;