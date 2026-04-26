import { Post, Topic } from "../types/models";

export default function post(): Post {

  return Object.assign({
    comments: [],
    created_at: new Date(),
    deleted_at: null,
    html: null,
    id: faker.random.number(),
    is_accepted: false,
    is_locked: false,
    is_read: false,
    is_subscribed: false,
    is_voted: false,
    permissions: {
      write: true,
      delete: false,
      update: false,
      merge: false,
      adm_access: false
    },
    score: 0,
    text: null,
    updated_at: new Date(),
    url: "",
  }, props);
}

export function topic(props?: any): Topic {

  return Object.assign({
    id: faker.random.number(),
    title: faker.string(),
    is_locked: false,
    is_read: false,
    is_sticky: false,
    first_post_id: faker.random.number()
  }, props);
}
