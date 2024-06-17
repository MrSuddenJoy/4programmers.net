import Vue from "vue";
import store from "../../store";
import VueSection from '../../components/forum/section.vue';
import VueTopic from "../../components/forum/topic.vue";
import { mapState } from "vuex";
import { Forum, Topic } from '@/types/models';

type ForumGroup = {
  [key in number]: Forum;
};

export default Vue.extend({
  delimiters: ['${', '}'],
  store,
  data: () => ({
    collapse: window["collapse"] || {},
    postsPerPage: window.postsPerPage || null,
    flags: window.flags || [],
    showCategoryName: window.showCategoryName || false,
    groupStickyTopics: window.groupStickyTopics || false,
    tags: window.tags || {}
  }),
  components: {
    'vue-section': VueSection,
    'vue-topic': VueTopic
  },
  created() {
    store.commit('forums/init', window.forums || []);
    store.commit('topics/init', (window.topics?.data) || []);
    store.commit('flags/init', window.flags);
  },
  methods: {
    changeCollapse(id: number): void {
      this.$set(this.collapse, id, !(!!(this.collapse[id])));
    },

    containsUserTags(topic: Topic): boolean {
      if (!topic.tags) {
        return false;
      }

      // @ts-expect-error
      return topic.tags.filter(tag => this.tagNames.includes(tag.name)).length > 0;
    }
  },
  computed: {
    forums(): Forum[] {
      return store.state.forums.categories;
    },

    sections(): Forum[] {
      return Object.values(
        <Forum>
        this
          .forums
          .sort((a, b) => a.order < b.order ? -1 : 1)
          .reduce((acc, forum) => {
            if (!acc[forum.section]) {
              acc[forum.section] = {name: forum.section, order: forum.order, categories: [], isCollapse: !!(this.collapse[forum.id])};
            }

            acc[forum.section].categories.push(forum);

            return acc;
          }, {})
      ).sort((a, b) => (a as Forum).order < (b as Forum).order ? -1 : 1); // sort sections
    },

    groups(): ForumGroup {
      return this.topics.reduce((acc, item) => {
        let index = this.groupStickyTopics ? (+!item.is_sticky) : 0;

        if (!acc[index]) {
          acc[index] = [];
        }

        acc[index].push(item);

        return acc;
      }, {});
    },

    tagNames(): string[] {
      return this.tags.map(tag => tag.name);
    },

    ...mapState('topics', ['topics']),
  }
});
