<template>
    <li class="list-group-item " v-bind:class="{ active: isActive }">
        <div class="smalltile">
            <a :href="redirectUrl">
                <img v-bind:src="user.personinfo.avatar">
                <div class="smalltile-name">{{ user.personinfo.name }}</div>
                <div v-if="user.unreadcnt > 0" class="unreadcnt">{{ user.unreadcnt }}</div>
                <div v-if="user.onlinestatus" class="online"><i class="fas fa-wifi"></i></div>
                <div v-if="!user.onlinestatus" class="offline">offline</div>
            </a>
        </div>
    </li>
</template>

<script>
    export default {
        props:['user', 'isroom', 'to', 'baseurl'],
        data(){
            return {
                redirectUrl:"",
                isActive:false
            }
        },
        mounted(){
            if(this.isroom){
                if(this.to != undefined && this.user.id == this.to.id){
                    this.isActive = true;
                }
            }

            this.redirectUrl=this.baseurl;
            this.redirectUrl=this.redirectUrl.replace('#to#', this.user.id);
        }
    }
</script>
