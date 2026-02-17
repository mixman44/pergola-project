<?php

namespace App\Ai\Prompts;

class GeneratePergolaPrompt implements PromptInterface
{
    private string $description =
        "- Type: free-standing pergola (autoportée)\n" .
        "- Shape: rectangle\n" .
        "- Structure:\n" .
        "  - STRICTLY FOUR vertical posts\n" .
        "  - NO additional posts, supports or columns\n" .
        "  - horizontal beams connecting the four posts\n" .
        "  - open slatted roof (clear-voie)\n" .
        "- Material: aluminum\n" .
        "- Color: anthracite grey (RAL 4401)\n" .
        "- Finish: matte architectural outdoor finish\n";

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getPrompt(): string
    {
        return
            "You are an architectural visualization AI working in photo-realistic image editing mode.\n\n" .

            "IMPORTANT:\n" .
            "This is NOT a creative task.\n" .
            "This is a strict geometric integration task.\n\n" .

            "You MUST preserve:\n" .
            "- the original camera position\n" .
            "- the original perspective\n" .
            "- the original focal length\n" .
            "- the original scale\n" .
            "- the original geometry of the scene\n" .

            "The provided image contains a predefined RED rectangular shape.\n" .
            "This RED shape is the ONLY valid geometric reference.\n\n" .

            "The pergola MUST match the RED shape EXACTLY.\n\n" .

            "Pergola specification:\n" .
            $this->description . "\n" .

            "ABSOLUTE CONSTRAINTS:\n" .
            "- DO NOT change camera angle or perspective\n" .
            "- DO NOT add extra vertical elements\n" .
            "- The pergola MUST have STRICTLY FOUR posts\n";
    }
}
