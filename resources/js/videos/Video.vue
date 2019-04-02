
<template>
    <layout :title="series.name" :user="user">
      <div id="video">
        <iframe @click="alert('ahh')" frameborder="0" scrolling="no" width="100%" height="510px" :src="mirror" allowfullscreen></iframe>
      </div>
        <h1><a :href="`/series/${series.id}-${ series.name }`">{{ series.name }}</a> - EP {{ video.episode }} <span><a v-if="video.episode !== '1'" :href="`/watch/${series.id}/${series.name}/${video.episode-1}`"><i class="fas fa-chevron-left"></i> Previous Episode | </a> <a :href="`/watch/${series.id}/${series.name}/${next.next}`">Next Episode <i class="fas fa-chevron-right"></i></a></span></h1>
        <select v-model="mirror">
            <option v-for="mirror in video.mirrors" :key="mirror.id" :value="mirror.src">{{mirror.host}} - {{ mirror.quality }}</option>
        </select>
    </layout>
</template>

<style scoped>
    #video {
        background: #000;
        margin-bottom: 1rem;
    }
    h1 span {
        float: right;
        font-size: 1rem;
        text-transform: none;
        font-family: 'Source Sans Pro', sans-serif;
    }

    h1 span i {
        font-size: 0.75rem;
    }
</style>

<script>
import axios from 'axios'
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
export default {
    props: ['video', 'series', 'next', 'user'],
    data() {
        return {
            mirror: this.video.mirrors[0].src,
            videoData: {
                user_id: this.user !== null ? this.user.id : null,
                series_id: this.series.id,
                episode: this.video.episode,
            },
        }
    },
    methods: {
        setWatched: function() {
            axios.post(`/watch/addrecent`, this.videoData).then(response => {
                console.log(response);
                }).catch((error) => {
                    console.log(this.videoData);
                console.log(error);
            });
            clearTimeout(this.timer);
        }
    },
    beforeMount() {
        var self = this;
        this.timer = setTimeout(function() {
            self.user !== null ? self.setWatched() : null
        }, 900000);
    },
    beforeDestroy() {
        clearTimeout(this.timer);
    }
}
</script>