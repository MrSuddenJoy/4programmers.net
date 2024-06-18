import Prism from 'prismjs';
import { Vue, Component, Prop, Emit, Ref } from "vue-property-decorator";
import store from "@/store";
import { Microblog, User } from "@/types/models";
import VueModal from "../modal.vue";
import VueForm from "../microblog/form.vue";

@Component
export class MicroblogMixin extends Vue {
  protected isWrapped = false;

  @Prop({required: false})
  protected microblog!: Microblog;

  @Ref()
  protected readonly confirm!: VueModal;

  @Ref('block')
  protected readonly blockModal!: VueModal;

  @Ref()
  protected readonly form!: VueForm;

  protected edit(microblog: Microblog) {
    store.commit('microblogs/TOGGLE_EDIT', microblog);

    if (microblog.is_editing) {
      // @ts-expect-error
      this.$nextTick(() => this.form.markdown.focus());
      this.isWrapped = false;
    }
  }

  protected delete(action: string, microblog: Microblog) {
    this.$confirm({
      message: 'Tej operacji nie będzie można cofnąć.',
      title: 'Usunąć wpis?',
      okLabel: 'Tak, usuń'
    })
    .then(() => store.dispatch(action, microblog));
  }

  protected block(user: User) {
    this.$confirm({
      message: 'Nie będziesz widział komentarzy ani wpisów tego użytkownika',
      title: 'Zablokować użytkownika?',
      okLabel: 'Tak, zablokuj'
    })
    .then(() => {
      store.dispatch('user/block', user.id);

      this.$notify({type: 'success', duration: 5000, title: 'Gotowe!', text: '<a href="javascript:" onclick="window.location.reload();">Przeładuj stronę, aby odświeżyć wyniki</a>.'})
    });
  }

  protected splice(users?: string[]): null | string {
    if (!users?.length) {
      return null;
    }

    return users.length > 10 ? users.splice(0, 10).concat('...').join("\n") : users.join("\n");
  }
}

@Component
export class MicroblogFormMixin extends Vue {
  isProcessing = false;

  @Prop({default() {
    return {
      assets: [],
      tags: []
    }
  }})
  microblog!: Microblog;

  @Emit()
  cancel() {
    //
  }

  protected save(action) {
    this.isProcessing = true;

    return store.dispatch(action, this.microblog)
      .then(result => {
        this.$emit('save', result.data);

        if (!this.microblog.id) {
          this.microblog.text = '';
          this.microblog.assets = [];
          this.microblog.tags = [];
        }

        // highlight once again after saving
        this.$nextTick(() => Prism.highlightAll());
      })
      .finally(() => this.isProcessing = false);
  }
}
