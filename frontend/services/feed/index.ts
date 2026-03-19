import API from "../api/API";

export const getFeeds = async (page: number = 1) => {
  try {
    const res = await API.get(`/feed?page=${page}`);
    return res.data;
  } catch (err) {
    
  }
};
