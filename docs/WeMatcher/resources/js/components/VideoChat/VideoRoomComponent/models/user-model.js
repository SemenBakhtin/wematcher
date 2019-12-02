class UserModel {
    connectionId;
    audioActive;
    videoActive;
    screenShareActive;
    nickname;
    email;
    verified;
    age;
    country;
    avatar;
    streamManager;
    type; // 'remote' | 'local'

    constructor() {
        this.connectionId = '';
        this.audioActive = true;
        this.videoActive = true;
        this.screenShareActive = false;
        this.nickname = '';
        this.email = '';
        this.verified = false;
        this.age = '';
        this.avatar = '';
        this.streamManager = null;
        this.type = 'local';
    }

    isAudioActive() {
        return this.audioActive;
    }

    isVideoActive() {
        return this.videoActive;
    }

    isScreenShareActive() {
        return this.screenShareActive;
    }

    getConnectionId() {
        return this.connectionId;
    }

    getNickname() {
        return this.nickname;
    }

    getEmail() {
        return this.email;
    }

    getCountry() {
        return this.country;
    }

    getVerified() {
        return this.verified;
    }

    getAge() {
        return this.age;
    }

    getAvatar() {
        return this.avatar;
    }

    getStreamManager() {
        return this.streamManager;
    }

    isLocal() {
        return this.type === 'local';
    }
    isRemote() {
        return !this.isLocal();
    }
    setAudioActive(isAudioActive) {
        this.audioActive = isAudioActive;
    }
    setVideoActive(isVideoActive) {
        this.videoActive = isVideoActive;
    }
    setScreenShareActive(isScreenShareActive) {
        this.screenShareActive = isScreenShareActive;
    }
    setStreamManager(streamManager) {
        this.streamManager = streamManager;
    }

    setConnectionId(conecctionId) {
        this.connectionId = conecctionId;
    }
    setNickname(nickname) {
        this.nickname = nickname;
    }
    setEmail(email) {
        this.email = email;
    }
    setCountry(country) {
        this.country = country;
    }
    setVerified(verified) {
        this.verified = verified;
    }
    setAge(age) {
        this.age = age;
    }
    setAvatar(avatar) {
        this.avatar = avatar;
    }
    setType(type) {
        if (type === 'local' |  type === 'remote') {
            this.type = type;
        }
    }
}

export default UserModel;
