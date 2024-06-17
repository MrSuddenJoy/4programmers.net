import VueForm from "@/components/forum/form.vue";
import VuePoll from "@/components/forum/poll.vue";
import VuePostWrapper from "@/components/forum/post-wrapper.vue";
import VuePagination from "@/components/pagination.vue";
import {PostCommentSaved, PostSaved, PostVoted, Subscriber} from "@/libs/live";
import store from "@/store";
import {Post} from "@/types/models.ts";
import Vue from "vue";
import {mapGetters, mapState} from "vuex";

export default Vue.extend({
  delimiters: ['${', '}'],
  components: {
    'vue-post-wrapper': VuePostWrapper,
    'vue-form': VueForm,
    'vue-poll': VuePoll,
    'vue-pagination': VuePagination
  },
  store,
  data: () => ({
    showStickyCheckbox: window.showStickyCheckbox,
    undefinedPost: {text: '', html: '', assets: []},
    reasons: window.reasons,
    popularTags: window.popularTags
  }),
  created() {
    store.commit('posts/init', window.pagination);
    store.commit('topics/init', [window.topic]);
    store.commit('topics/setReasons', this.reasons);
    store.commit('forums/init', [window.forum]);
    store.commit('poll/init', window.poll);
    store.commit('flags/init', window.flags);
  },
  mounted() {
    document.getElementById('js-skeleton')?.remove();

    this.liveUpdate();

    const hints = ['hint-title', 'hint-text', 'hint-tags', 'hint-user_name'];

    [
      document.querySelector('#js-submit-form input[name="title"]'),
      document.querySelector('#js-submit-form textarea[name="text"]'),
      document.querySelector('#js-submit-form input[name="tags"]')
    ].forEach(el => {
      if (!el) {
        return;
      }

      el.addEventListener('focus', () => {
        const name = el.getAttribute('name');
        const hint = document.getElementById(`hint-${name}`);

        if (!hint) {
          return; // hint tooltips might not be present on the website
        }

        hints.forEach(hint => document.getElementById(hint)!.style.display = 'none');
        hint.style.display = 'block';
      });
    });
  },
  methods: {
    liveUpdate() {
      const subscriber = new Subscriber(`topic:${window.topic.id}`);

      subscriber.subscribe('CommentSaved', new PostCommentSaved())
      subscriber.subscribe('PostSaved', new PostSaved())
      subscriber.subscribe('PostVoted', new PostVoted())
    },

    redirectToTopic(post: Post) {
      this.resetPost(post);

      window.location.href = post.url;
    },

    changePage(page: number) {
      window.location.href = `?page=${page}`;
    },

    reply(post: Post, scrollIntoForm = true) {
      const username = post.user ? post.user.name : post.user_name!;

      if (scrollIntoForm) {
        this.markdownRef.appendUserMention(username);
        document.getElementById('js-submit-form')!.scrollIntoView();
      } else {
        this.markdownRef.appendBlockQuote(username, post.id, post.text);
        this.$notify({type: 'success', text: 'Cytat został dodany do formularza.'});
      }
    },

    resetPost(post: Post) {
      this.undefinedPost = {text: '', html: '', assets: []};
      window.location.hash = `id${post.id}`;
    }
  },
  computed: {
    markdownRef(): VueMarkdown {
      // @ts-expect-error
      return this.$refs['js-submit-form'].$refs['markdown']!;
    },
    ...mapGetters('posts', ['posts', 'totalPages', 'currentPage']),
    ...mapGetters('topics', ['topic']),
    ...mapGetters('user', ['isAuthorized']),
    ...mapState('poll', ['poll'])
  }
});
