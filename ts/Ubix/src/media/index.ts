// Media transport primitives: WHIP publish, WHEP/HLS playback.
//
// Reserved, empty. `whip.ts` and the player component are specified by the host's
// live-streaming surface and land with it (M3), not ahead of it.
//
// Mechanism only when they arrive: these speak the WHIP/WHEP drafts and HLS to
// whatever media server the host points them at. They know nothing about who is
// allowed to watch -- entitlement, paywalls and tier precedence stay in the host.

export {};
