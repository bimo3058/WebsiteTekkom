import { describe, expect, test } from "bun:test";
import { createSingleFlightByKey } from "./single-flight";

describe("createSingleFlightByKey", () => {
  test("reuses the same request for the same key", async () => {
    let calls = 0;
    const exchange = createSingleFlightByKey(async (key) => {
      calls += 1;
      return `response:${key}`;
    });

    const first = exchange("one-time-token");
    const second = exchange("one-time-token");

    expect(second).toBe(first);
    expect(await second).toBe("response:one-time-token");
    expect(calls).toBe(1);
  });

  test("starts a new request for a different key", async () => {
    let calls = 0;
    const exchange = createSingleFlightByKey(async (key) => {
      calls += 1;
      return key;
    });

    await exchange("first-token");
    await exchange("second-token");

    expect(calls).toBe(2);
  });
});
