<template>
  <div v-on-clickaway="blurInput" :class="{'nav-search-mobile': isMobile}" class="nav-search">
    <div :class="{'active': isActive}" class="search-bar ml-md-4 mr-md-4">
      <i class="fas fa-search ml-2 mr-2"></i>

      <form :action="url" role="search" ref="search" class="flex-grow-1">
        <input
          ref="input"
          @focus="showDropdown"
          @keydown.esc.prevent="hideDropdown"
          @keyup="completion"
          @keyup.up.prevent="up"
          @keyup.down.prevent="down"
          @keydown.enter.prevent="changeUrl"
          @search="clearInput"
          v-model="value"
          type="search"
          name="q"
          autocomplete="off"
          placeholder="Wpisz &quot;?&quot; aby uzyskac pomoc lub wyszukaj"
        >
      </form>

      <!-- close mobile menu -->
      <button v-if="isMobile" @click="toggleMobile" class="btn nav-link">
        <i class="fa fa-2x fa-times"></i>
      </button>

      <div v-if="isHelpEnabled" class="search-dropdown p-3">
        <div class="row">
          <div class="col-6 mb-2">
            <code>"foo bar"</code> <small class="text-muted">szukaj całych fraz</small>
          </div>

          <div class="col-6 mb-2">
            <code>+foo -bar</code> <small class="text-muted">wyklucz lub żądaj danego słowa w dokumencie</small>
          </div>

          <div class="col-6 mb-2">
            <code>foo*</code> <small class="text-muted">szuka fragmentów słów</small>
          </div>

          <div class="col-6 mb-2">
            <code>foo~</code> <small class="text-muted">szuka podobnych słów</small>
          </div>

          <div class="col-6 mb-2">
            <code>user:foo</code> <small class="text-muted">szukaj po autorze</small>
          </div>
        </div>
      </div>

      <div v-else-if="isDropdownVisible && items.length > 0" class="search-dropdown">
<!--&lt;!&ndash;      <div v-if="isDropdownVisible && items.length > 0" class="search-dropdown">&ndash;&gt;-->
<!--&lt;!&ndash;        <nav class="list-inline" style="margin: 10px 7px 10px 7px">&ndash;&gt;-->
<!--&lt;!&ndash;          <a class="list-inline-item active mr-2 text-primary" href="#" style="padding: 5px; font-size: 90%; border-bottom: 2px solid #80a41a">Forum</a>&ndash;&gt;-->
<!--&lt;!&ndash;          <a class="list-inline-item text-body" href="#" style="padding: 5px; font-size: 90%;">Praca</a>&ndash;&gt;-->
<!--&lt;!&ndash;        </nav>&ndash;&gt;-->

        <ul class="list-unstyled">
          <template v-for="category in categories">
            <li class="title"><span>{{ categoryLabel(category) }}</span></li>

            <li v-for="child in category.children" :class="{'hover': child.index === selectedIndex}" @mouseover="hoverItem(child.index)">
              <component :is="makeDecorator(child)" :item="child" :value="value"></component>
            </li>
          </template>
        </ul>
      </div>
    </div>

    <!-- show this only on mobile devices to show search bar -->
    <div v-if="!isMobile" class="d-md-none navbar-nav ml-auto mr-2">
      <a @click="toggleMobile" href="javascript:" class="nav-link">
        <i class="fa fa-search fa-fw"></i>
      </a>
    </div>
  </div>
</template>

<script lang="ts">
  import Vue from 'vue';
  import { mixin as clickaway } from 'vue-clickaway';
  import axios from 'axios';
  import store from '../../store';
  import { Hit } from '../../types/hit';
  import { SpecialKeys } from '../../types/keys';
  import Component from 'vue-class-component';
  import { Prop, Ref, Watch } from 'vue-property-decorator';
  import { mapGetters } from "vuex";
  import VueTopicDecorator from './decorators/topic';
  import VueJobDecorator from './decorators/job';
  import VueMicroblogDecorator from './decorators/microblog';
  import VueWikiDecorator from './decorators/wiki';
  import VueUserDecorator from './decorators/user';
  import { HitCategory, Contexts, Models } from '../../types/search';

  @Component({
    name: 'app',
    mixins: [clickaway],
    store,
    computed: mapGetters('user', ['isAuthorized']),
    components: {
      'vue-topic-decorator': VueTopicDecorator,
      'vue-job-decorator': VueJobDecorator,
      'vue-wiki-decorator': VueWikiDecorator,
      'vue-microblog-decorator': VueMicroblogDecorator,
      'vue-user-decorator': VueUserDecorator
    }
  })
  export default class VueSearchbar extends Vue {
    isActive: boolean = false;
    isDropdownVisible: boolean = false;
    isHelpEnabled: boolean = false;
    isMobile: boolean = false;
    items: Hit[] = [];
    selectedIndex: number = -1;

    readonly isAuthorized! : boolean;

    @Prop(String)
    readonly url!: string;

    @Prop()
    value!: string;

    @Ref('input')
    readonly input!: HTMLInputElement;

    @Watch('value')
    resetItems(val: string) {
      if (val === '') {
        this.items = [];
      }
    }

    mounted() {
      document.addEventListener('keydown', this.shortcutSupport);
    }

    beforeDestroy() {
      document.removeEventListener('keydown', this.shortcutSupport);
    }

    toggleMobile() {
      this.isMobile = ! this.isMobile;

      if (this.isMobile) {
        this.$nextTick(() => (this.$refs.input as HTMLInputElement).focus());
      }
    }

    showDropdown() {
      this.isActive = true;
      this.isDropdownVisible = true;

      // show basic set of links for given user even if no query was provided
      this.loadItems();
    }

    hideDropdown() {
      if (this.isDropdownVisible) {
        this.isDropdownVisible = false;
      }
      else {
        this.value = '';
      }
    }

    blurInput(): void {
      this.isActive = false;
    }

    clearInput() {
      this.value = '';
      this.loadItems();
    }

    down() {
      this.isDropdownVisible = true;

      this.changeIndex(++this.selectedIndex);
    }

    up() {
      this.changeIndex(--this.selectedIndex);
    }

    hoverItem(index) {
      this.selectedIndex = index;
    }

    changeIndex(index) {
      const length = this.items.length;

      if (length > 0) {
        if (index >= length) {
          index = 0;
        }
        else if (index < 0) {
          index = length - 1;
        }

        this.selectedIndex = index;
      }
    }

    categoryLabel(category: Hit): string {
      return category.context !== undefined ? Contexts[category.model][category.context] : Models[category.model];
    }

    completion(event: KeyboardEvent): void {
      this.isHelpEnabled = this.value === '?';

      if (this.isHelpEnabled || Object.values(SpecialKeys).includes(event.keyCode)) {
        return;
      }

      this.selectedIndex = -1; // reset position index after key pressed
      this.loadItems();
    }

    loadItems(): void {
      const headers = this.isAuthorized ? {Authorization: `Bearer ${this.$store.state.user.token}`} : {};

      if (!this.endpoint) {
        return;
      }

      axios.get(this.endpoint, {params: {q: this.value || null}, headers}).then(response => {
        this.items = response.data;
        this.isDropdownVisible = true;
      });
    }

    shortcutSupport(event: KeyboardEvent): void {
      if (event.key === '?' && event.shiftKey && (!/^(?:input|textarea|select|button)$/i.test((event.target as HTMLElement).tagName))) {
        event.preventDefault();

        (this.$refs.input as HTMLInputElement).focus();
      }
    }

    changeUrl(): void {
      if (this.selectedIndex === -1) {
        (this.$refs.search as HTMLFormElement).submit();

        return;
      }

      window.location.href = this.items.find(item => item.index === this.selectedIndex)!.url;
    }

    makeDecorator(item: Hit): string {
      return `vue-${item.model.toLowerCase()}-decorator`;
    }

    get endpoint(): string | null {
      return this.value.trim() === '' ? (this.isAuthorized ? '/completion/hub/' : null) : '/completion/';
    }

    get categories() {
      let counter = 0;
      let result: HitCategory = {};

      this.items.forEach(item => {
        const key = `${item.model}-${item.context}`;
        let model, context;

        ({model, context} = item);

        if (!result[key]) {
          result[key] = {children: [], model, context}
        }

        result[key].children.push(item);
      });

      return Object.values(result).map(category => {
        category.children.map(child => Object.assign(child, { index: counter++ }));

        return category;
      });
    }
  }
</script>

<style lang="scss">

@import "@/sass/helpers/_variables.scss";
@import "~bootstrap/scss/functions";
@import "~bootstrap/scss/variables";
@import "~bootstrap/scss/mixins/breakpoints";

.nav-search {
  width: 100%;
  align-items: center;
  display: flex;

  .search-bar {
    display: flex;
    align-items: center;
    width: 100%;
    height: 35px;
    position: relative;
    border-radius: 3px;
    padding: 5px;
    background-color: var(--header-search-bg);
    border: 1px solid var(--header-search-border);

    &:after {
      content: 'Shift+/';
      position: absolute;
      right: 25px;
      top: 30%;
      text-transform: uppercase;
      font-size: 10px;
      color: var(--header-color);
    }

    .fa-search {
      color: var(--header-color);
    }
  }

  input {
    background-color: var(--header-search-bg);
    padding: 0;
    color: var(--header-color);
    width: 100%;
    line-height: 22px;
    outline: 0;
    font-size: .8rem;
    border: none;
  }

  .active {
    &.search-bar {
      background-color: #fff;

      &:after {
        display: none; // temp code
      }
    }

    .fa-search {
      color: $gray-700 !important;
    }

    .search-dropdown {
      display: block;
    }
  }
}

.nav-search:not(.nav-search-mobile) {
  @include media-breakpoint-down('sm') {
    .search-bar {
      display: none;
    }
  }
}

.nav-search-mobile {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  z-index: 1000;

  .search-bar {
    height: 100%;
    border-radius: 0;

    &:after {
      display: none; // temp code
    }
  }

  .search-dropdown {
    margin-top: 1px; // add extra margin since input does not have border-radius anymore
  }

  input::placeholder, input::-webkit-input-placeholder {
    color: transparent;
  }
}

.search-dropdown {
  position: absolute;
  display: none;
  border: 1px solid $card-border-color;
  box-shadow: $dropdown-box-shadow;
  width: calc(100% + 2px);
  background-color: #fff;
  left: -1px;
  top: 100%;
  z-index: 100;

  .item-options {
    position: absolute;
    right: 10px;
    display: none;

    i {
      color: $text-muted;
    }
  }

  li {
    &.hover {
      background-color: $dropdown-link-hover-bg;

      .item-options {
        display: inline;
      }
    }

    > a {
      padding: 5px 10px;
      display: block;
      color: $gray-800;

      &:hover {
        text-decoration:  none;
        color: inherit;
      }
    }
  }

  .title {
    font-size: .7rem;
    margin: 5px 10px;
    color: #777;
    text-transform: lowercase;
    background: linear-gradient(to bottom, #ffffff 47%, #b4b4b4 50%, #ffffff 52%);

    > span {
      background-color: #fff;
      display: inline-block;
      padding: 2px 10px 2px 0;
    }
  }

  .forum-name {
    font-size: .65rem;
  }
}
</style>