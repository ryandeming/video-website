<template>
    <layout title="Videos" :user="user">
      <section class="videos">
        <header>
            <h1 class="text-primary"><span class="underline">Recent Videos</span></h1>
        </header>
        <div class="videos-list flex flex-wrap">
          <div class="card video-card" v-for="video in videos.data" :key="video.id">
            <a :href="`/watch/${video.series_id}/${ video.url }/${ video.episode }`">
              <div class="thumbnail">
                <span class="overlay-text">{{ video.time }}</span>
                <div class="overlay"></div>
                  <img class="lazy" :src="video.img" :alt="video.name">
              </div>
            </a>
            <a :href="`/series/${video.series_id}-${ video.url }`">
                <p><span class="text-primary left">{{ video.name }}</span> <span class="right">EP {{ video.episode }}</span></p>
            </a>
          </div>
        </div>
        <div class="pagination">
          <a class="prev" data-turbolinks-scroll="false" v-if="videos.current_page !== 1" :href="videos.prev_page_url"><i class="fas fa-chevron-left"></i></a> <a class="next" data-turbolinks-scroll="false" v-if="videos.current_page !== videos.last_page" :href="videos.next_page_url"><i class="fas fa-chevron-right"></i></a>
        </div>
      </section>
    </layout>
</template>

<style>
  .pagination {
    text-align: center;
    font-size: 32px;
  }

  .pagination a {
    transition: all 0.3s;
  }

  .pagination a:hover {
    color: #fff;
  }
  .prev, .next {
    width: 30px;
    text-align: center;
    display: inline-block;
  }
</style>
<script>
export default {
    props: ['videos', 'user'],
}
</script>