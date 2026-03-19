import { useCallback, useEffect, useRef, useState } from "react";
import { StyleSheet } from "react-native";

import { Text, View } from "@/components/Themed";
import API from "@/services/api/API";
import { SafeAreaView } from "react-native-safe-area-context";
import { getFeeds } from "@/services/feed";
import { Snackbar } from "react-native-paper";

export default function HomeScreen() {
  const [isLoading, setIsLoading] = useState(true);
  const [feedData, setFeedData] = useState([]);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetchFeedData();
  }, []);

  const fetchFeedData = useCallback(async () => {
    setIsLoading(true);
    try {
      const response = await getFeeds();
      setFeedData(response.data);
    } catch (err: Error | any) {
      setError(err?.message || "Failed to fetch feed data.");
    } finally {
      setIsLoading(false);
    }
  }, []);

  return (
    <SafeAreaView>
      {/* <Snackbar
        visible={!error}
        duration={3000}
        onDismiss={() => {
          setError(null);
        }}
      ></Snackbar> */}
      {/* {error} */}
      <Text>Ia`m Fayyad</Text>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
  },
  title: {
    fontSize: 20,
    fontWeight: "bold",
  },
  separator: {
    marginVertical: 30,
    height: 1,
    width: "80%",
  },
});
