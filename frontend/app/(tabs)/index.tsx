import { useEffect } from "react";
import { StyleSheet } from "react-native";

import { Text, View } from "@/components/Themed";
import API from "@/services/api/API";

export default function HomeScreen() {
  console.log("hello");
  useEffect(() => {
    const fetchHello = async () => {
      try {
        const response = await API.get("/hello");
        console.log("Backend hello API response:", response.data);
      } catch (error) {
        console.error("Failed to fetch backend hello API:", error);
      }
    };

    fetchHello();
  }, []);

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Home Screen</Text>
    </View>
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
