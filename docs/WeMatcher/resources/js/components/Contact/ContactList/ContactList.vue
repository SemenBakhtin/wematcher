<template>
    <ul class="list-group">
        <contact-item v-for="user in contacts" v-bind:data="user" v-bind:key="user.id" :user="user" :isroom="isroom" :to="to" :baseurl='baseurl'></contact-item>
    </ul>
</template>

<script>
    import ContactItem from '../ContactItem/ContactItem.vue';
    export default {
        props:['contacts', 'isroom', 'to', 'baseurl'],
        components:{ContactItem},
        data(){
            return {
                users: []
            }
        },
        mounted() {
            let contactids = [];
            for(var i=0; i<this.contacts.length; i++){
                contactids.push(this.contacts[i].id);
            }

            this.users = this.contacts;

            Echo.join('online')
                .here(users => {
                    let userids = [];
                    for(var i=0; i<users.length; i++){
                        userids.push(users[i].id);
                    }

                    for(var i=0; i<this.users.length; i++){
                        if(userids.indexOf(this.users[i].id) > -1){
                            this.users[i].onlinestatus = true;
                        }
                        else{
                            this.users[i].onlinestatus = false;
                        }
                    }
                })
                .joining(user => {
                    let index = contactids.indexOf(user.id);
                    if(index > -1){
                        this.users[index].onlinestatus = true;
                    }
                })
                .leaving(user => {
                    let index = contactids.indexOf(user.id);
                    if(index > -1){
                        this.users[index].onlinestatus = false;
                    }
                })
        }
    }
</script>
