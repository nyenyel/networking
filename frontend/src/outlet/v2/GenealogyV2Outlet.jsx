import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import FamilyTree from 'react-native-family-tree';

const data = [
  {
    id: 1,
    name: 'John Doe',
    children: [
      {
        id: 2,
        name: 'Jane Doe',
        children: [
          {
            id: 3,
            name: 'Michael Doe',
          },
        ],
      },
    ],
  },
];

export default function GenealogyV2Outlet() {
  const customNode = (node) => {
    return (
      <View style={styles.node}>
        <Text style={styles.nodeText}>{node.name}</Text>
      </View>
    );
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Family Tree</Text>
      <FamilyTree
        data={data}
        nodeComponent={customNode}
        lineColor="blue"
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  title: {
    fontSize: 24,
    marginBottom: 20,
  },
  node: {
    padding: 10,
    borderWidth: 1,
    borderColor: 'black',
    borderRadius: 5,
    backgroundColor: 'lightgray',
  },
  nodeText: {
    fontWeight: 'bold',
  },
});